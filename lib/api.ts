import axios, { AxiosError, AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios';
import { useAuthStore } from './stores/authStore';

// API response and error interfaces
export interface ApiResponse<T = any> {
  data: T;
  status: number;
  statusText: string;
  headers: any;
}

export interface ApiError {
  message: string;
  status?: number;
  errors?: Record<string, string[]>;
  originalError?: Error;
}

// Configuration interface
export interface ApiClientConfig {
  baseURL: string;
  timeout?: number;
  getToken?: () => string | null | Promise<string | null>;
}

export class ApiClient {
  private client: AxiosInstance;
  private getToken: ApiClientConfig['getToken'];

  constructor(config: ApiClientConfig) {
    const { baseURL, timeout = 30000, getToken } = config;
    this.getToken = getToken;
    this.client = axios.create({
      baseURL,
      timeout,
      withCredentials: true,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });
    console.log('this.client', this.client)

    this.setupInterceptors();
  }

  private setupInterceptors(): void {
    // Request interceptor to add auth token
    this.client.interceptors.request.use(
      async (config) => {
        console.log('config', config, document.cookie)
        if (this.getToken) {
          const token = await this.getToken();
          if (token) {
            config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
            config.headers['Content-Type'] = 'application/json';
          }
        }

        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor
    this.client.interceptors.response.use(
      (response) => {
        console.log('✅ Response intercepted:', response); // ✅ Log successful response
        return response;
      },
      (error: AxiosError) => {
        // Handle unauthorized errors globally
        if (error.response?.status === 401) {
          // You can trigger a sign-out action or redirect here
          console.error('Authentication error: User is not authenticated');
          
          // If you have a global state management or auth context:
          // authService.signOut();
          // Or use Next.js router to redirect
          // window.location.href = '/login';
        }
        
        return Promise.reject(this.normalizeError(error));
      }
    );
  }

  private normalizeError(error: AxiosError): ApiError {
    const apiError: ApiError = {
      message: 'An unexpected error occurred',
      originalError: error,
    };

    if (error.response) {
      // The request was made and the server responded with a status code
      // that falls out of the range of 2xx
      apiError.status = error.response.status;
      apiError.message = (error.response.data as any)?.message || `Error: ${error.response.status}`;
      
      // Handle Laravel validation errors format
      if ((error.response.data as any)?.errors) {
        apiError.errors = (error.response.data as any).errors;
      }
    } else if (error.request) {
      // The request was made but no response was received
      apiError.message = 'No response received from server';
    }

    return apiError;
  }

  private formatResponse<T>(response: AxiosResponse): ApiResponse<T> {
    return {
      data: response.data,
      status: response.status,
      statusText: response.statusText,
      headers: response.headers,
    };
  }

  // GET request
  async get<T = any>(
    url: string,
    params?: Record<string, any>,
    config?: AxiosRequestConfig
  ): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.get<T>(url, { ...config, params });
      return this.formatResponse<T>(response);
    } catch (error) {
      throw error;
    }
  }

  // POST request
  async post<T = any>(
    url: string,
    data?: any,
    config?: AxiosRequestConfig
  ): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.post<T>(url, data, config);
      return this.formatResponse<T>(response);
    } catch (error) {
      console.log('error', error)
      throw error;
    }
  }

  // PUT request
  async put<T = any>(
    url: string,
    data?: any,
    config?: AxiosRequestConfig
  ): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.put<T>(url, data, config);
      return this.formatResponse<T>(response);
    } catch (error) {
      throw error;
    }
  }

  // DELETE request
  async delete<T = any>(
    url: string,
    config?: AxiosRequestConfig
  ): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.delete<T>(url, config);
      return this.formatResponse<T>(response);
    } catch (error) {
      throw error;
    }
  }
}