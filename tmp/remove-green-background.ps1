param([string]$InputPath, [string]$OutputPath)

Add-Type -AssemblyName System.Drawing
Add-Type -TypeDefinition @'
using System;
using System.Drawing;
using System.Drawing.Imaging;

public static class MiCusinaChromaKey {
    public static void Remove(string input, string output) {
        using (var source = new Bitmap(input))
        using (var result = new Bitmap(source.Width, source.Height, PixelFormat.Format32bppArgb)) {
            for (int y = 0; y < source.Height; y++) {
                for (int x = 0; x < source.Width; x++) {
                    Color p = source.GetPixel(x, y);
                    int dominance = p.G - Math.Max(p.R, p.B);
                    int alpha = (p.G >= 242 && dominance >= 165)
                        ? 0
                        : (p.G >= 205 && dominance > 55)
                            ? (int)(255 * (1.0 - Math.Min(1.0, (dominance - 55) / 110.0)))
                            : 255;
                    int green = alpha < 255 ? Math.Min(p.G, Math.Max(p.R, p.B)) : p.G;
                    result.SetPixel(x, y, Color.FromArgb(alpha, p.R, green, p.B));
                }
            }
            result.Save(output, ImageFormat.Png);
        }
    }
}
'@ -ReferencedAssemblies System.Drawing

[MiCusinaChromaKey]::Remove($InputPath, $OutputPath)
