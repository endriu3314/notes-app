package com.uav.notesapp;

import com.uav.notesapp.client.ApiClient;
import com.uav.notesapp.service.TokenManagerService;

public class Application extends android.app.Application {
    private static TokenManagerService tokenManagerService;
    private static ApiClient apiClient;

    @Override
    public void onCreate() {
        super.onCreate();

        tokenManagerService = new TokenManagerService(this);
        apiClient = new ApiClient(tokenManagerService);
    }

    public static TokenManagerService getTokenManagerService() {
        return tokenManagerService;
    }

    public static ApiClient getApiClient() {
        return apiClient;
    }
}
