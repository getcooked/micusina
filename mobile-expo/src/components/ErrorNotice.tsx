import { HelperText } from 'react-native-paper';
export function ErrorNotice({ message }: { message?: string }) { return message ? <HelperText type="error" visible>{message}</HelperText> : null; }
