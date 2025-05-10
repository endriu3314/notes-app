package com.uav.notesapp;

import com.uav.notesapp.client.ApiClient;
import com.uav.notesapp.model.User;
import com.uav.notesapp.service.TokenManagerService;

public class Application extends android.app.Application {

    private static TokenManagerService tokenManagerService;
    private static ApiClient apiClient;

    private static User authUser;

    @Override
    public void onCreate() {
        super.onCreate();

        tokenManagerService = new TokenManagerService(this);
        apiClient = new ApiClient(tokenManagerService);
    }

    public static User getAuthUser() {
        return authUser;
    }

    public static void setAuthUser(User user) {
        authUser = user;
    }

    public static TokenManagerService getTokenManagerService() {
        return tokenManagerService;
    }

    public static ApiClient getApiClient() {
        return apiClient;
    }
}
