package com.uav.notesapp.client;

import androidx.annotation.NonNull;
import com.uav.notesapp.service.TokenManagerService;
import java.io.IOException;
import okhttp3.Interceptor;
import okhttp3.Request;
import okhttp3.Response;

public class AuthInterceptor implements Interceptor {
    private TokenManagerService tokenManagerService;

    public AuthInterceptor(TokenManagerService tokenManger) {
        tokenManagerService = tokenManger;
    }

    @NonNull
    @Override
    public Response intercept(@NonNull Chain chain) throws IOException {
        Request originalRequest = chain.request();
        String token = tokenManagerService.getToken();

        if (token == null || token.isEmpty()) {
            return chain.proceed(originalRequest);
        }

        return chain.proceed(
                originalRequest.newBuilder().header("Authorization", "Bearer " + token).build());
    }
}
