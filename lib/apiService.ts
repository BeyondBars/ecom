import { ApiClient } from './api';
import { TokenStorage } from './tokenHelper';

// Create an instance with your API base URL
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
// Create and export the API client instance
const apiService = new ApiClient({
  baseURL: API_BASE_URL,
  timeout: 30000,
  getToken: TokenStorage.getToken
});

export default apiService;