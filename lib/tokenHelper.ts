
export const TokenStorage = {
  getToken: (): string | null => {
    if (typeof document === 'undefined') return null;

    const match = localStorage.getItem("auth_token");

    if (!match) return null;

    try {
      return decodeURIComponent(match.split('=')[1]);
    } catch (e) {
      console.error('Failed to decode XSRF-TOKEN:', e);
      return null;
    }
  },
  
  setToken: (token: string): void => {
    if (typeof window === 'undefined') return;
    localStorage.setItem('auth_token', token);
  },
  
  removeToken: (): void => {
    if (typeof window === 'undefined') return;
    localStorage.removeItem('auth_token');
  }
};