[CmdletBinding()]
param(
    [switch] $InitializeSigning,
    [switch] $StageWebsiteApk
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

if ($env:OS -ne 'Windows_NT') {
    throw 'This helper uses Windows account encryption. On CI, supply the four MICUSINA_RELEASE_* signing settings and run Gradle directly.'
}

$signingDirectory = Join-Path $PSScriptRoot '.signing'
$storeFile = Join-Path $signingDirectory 'micusina-production.p12'
$passwordFile = Join-Path $signingDirectory 'release-password.clixml'
$keyAlias = 'micusina-production'
$keytool = if ($env:JAVA_HOME) { Join-Path $env:JAVA_HOME 'bin\keytool.exe' } else { (Get-Command keytool.exe).Source }

if ($InitializeSigning) {
    if (Test-Path -LiteralPath $signingDirectory) {
        throw 'The .signing directory already exists. Refusing to replace signing material. Run without -InitializeSigning to reuse the existing key.'
    }

    New-Item -ItemType Directory -Path $signingDirectory | Out-Null
    $identity = [System.Security.Principal.WindowsIdentity]::GetCurrent()
    $acl = New-Object System.Security.AccessControl.DirectorySecurity
    $acl.SetOwner($identity.User)
    $acl.SetAccessRuleProtection($true, $false)
    foreach ($sid in @($identity.User, [System.Security.Principal.SecurityIdentifier]::new('S-1-5-18'))) {
        $rule = [System.Security.AccessControl.FileSystemAccessRule]::new(
            $sid, 'FullControl', 'ContainerInherit, ObjectInherit', 'None', 'Allow'
        )
        $acl.AddAccessRule($rule)
    }
    Set-Acl -LiteralPath $signingDirectory -AclObject $acl

    $randomBytes = New-Object byte[] 48
    $random = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    try { $random.GetBytes($randomBytes) } finally { $random.Dispose() }
    $securePassword = ConvertTo-SecureString ([Convert]::ToBase64String($randomBytes)) -AsPlainText -Force
    [Array]::Clear($randomBytes, 0, $randomBytes.Length)
    # Export-Clixml encrypts SecureString with DPAPI for this Windows account.
    $securePassword | Export-Clixml -LiteralPath $passwordFile

    $passwordPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
    $previousKeyPassword = [Environment]::GetEnvironmentVariable('MICUSINA_NEW_KEY_PASSWORD', 'Process')
    try {
        $env:MICUSINA_NEW_KEY_PASSWORD = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($passwordPointer)
        & $keytool -genkeypair -noprompt -keystore $storeFile -storetype PKCS12 `
            -alias $keyAlias -keyalg RSA -keysize 3072 -sigalg SHA256withRSA -validity 10000 `
            -dname 'CN=Mi Cusina, OU=Android, O=Mi Cusina' `
            -storepass:env MICUSINA_NEW_KEY_PASSWORD -keypass:env MICUSINA_NEW_KEY_PASSWORD
        if ($LASTEXITCODE -ne 0) { throw 'Release key generation failed. Existing signing material has been preserved for recovery.' }
    } finally {
        [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($passwordPointer)
        [Environment]::SetEnvironmentVariable('MICUSINA_NEW_KEY_PASSWORD', $previousKeyPassword, 'Process')
        $securePassword.Dispose()
    }
    Write-Host 'Created a new production signing key. Keep .signing private and arrange a secure, recoverable backup before distribution.'
}

if (!(Test-Path -LiteralPath $storeFile -PathType Leaf) -or !(Test-Path -LiteralPath $passwordFile -PathType Leaf)) {
    throw 'Local production signing is not initialized. Use -InitializeSigning only when creating a NEW app signing identity, or supply your existing key directly to Gradle.'
}

$securePassword = Import-Clixml -LiteralPath $passwordFile
if ($securePassword -isnot [System.Security.SecureString]) { throw 'The signing password is not stored as an encrypted SecureString.' }
$passwordPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
$settingNames = @('MICUSINA_RELEASE_STORE_FILE', 'MICUSINA_RELEASE_STORE_PASSWORD', 'MICUSINA_RELEASE_KEY_ALIAS', 'MICUSINA_RELEASE_KEY_PASSWORD')
$previousSettings = @{}
foreach ($name in $settingNames) { $previousSettings[$name] = [Environment]::GetEnvironmentVariable($name, 'Process') }

Push-Location $PSScriptRoot
try {
    $env:MICUSINA_RELEASE_STORE_FILE = $storeFile
    $env:MICUSINA_RELEASE_STORE_PASSWORD = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($passwordPointer)
    $env:MICUSINA_RELEASE_KEY_ALIAS = $keyAlias
    $env:MICUSINA_RELEASE_KEY_PASSWORD = $env:MICUSINA_RELEASE_STORE_PASSWORD
    $tasks = @(':app:lintRelease', ':app:testDebugUnitTest', ':app:assembleRelease', ':app:bundleRelease')
    if ($StageWebsiteApk) { $tasks += ':app:stageWebsiteApk' }
    & .\gradlew.bat --no-daemon @tasks
    if ($LASTEXITCODE -ne 0) { throw 'Release verification/build failed. Do not distribute this build.' }
} finally {
    Pop-Location
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($passwordPointer)
    $securePassword.Dispose()
    foreach ($name in $settingNames) { [Environment]::SetEnvironmentVariable($name, $previousSettings[$name], 'Process') }
}
