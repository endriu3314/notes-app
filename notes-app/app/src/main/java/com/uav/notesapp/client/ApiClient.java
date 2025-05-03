package com.uav.notesapp.client;

import com.uav.notesapp.service.ApiService;
import com.uav.notesapp.service.TokenManagerService;

import java.util.concurrent.TimeUnit;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ApiClient {
    private static final String BASE_URL = "http://192.168.68.66/api/";

    private Retrofit retrofit = null;
    private ApiService apiService = null;
    private TokenManagerService tokenManagerService;

    public ApiClient(TokenManagerService tokenManager) {
        tokenManagerService = tokenManager;
        initializeRetrofit();
    }

    private void initializeRetrofit() {
        if (retrofit != null) {
            return;
        }

        HttpLoggingInterceptor logging = new HttpLoggingInterceptor();
        logging.setLevel(HttpLoggingInterceptor.Level.BODY);

        AuthInterceptor authInterceptor = new AuthInterceptor(tokenManagerService);

        OkHttpClient.Builder httpClient = new OkHttpClient.Builder();
        httpClient.addInterceptor(logging);
        httpClient.addInterceptor(authInterceptor);
        httpClient.connectTimeout(30, TimeUnit.SECONDS);
        httpClient.readTimeout(30, TimeUnit.SECONDS);

        retrofit = new Retrofit.Builder()
                .baseUrl(BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .client(httpClient.build())
                .build();

        apiService = retrofit.create(ApiService.class);
    }

    public ApiService getApiService() {
        if (apiService == null) {
            initializeRetrofit();
        }

        return apiService;
    }

    public Retrofit getRetrofit() {
        if (retrofit == null) {
            initializeRetrofit();
        }

        return retrofit;
    }
}
