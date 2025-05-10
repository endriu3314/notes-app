package com.uav.notesapp.model.response.auth;

import com.google.gson.annotations.SerializedName;

public class LoginResponse {

    private String message;
    private String token;

    @SerializedName("expiresAt")
    private String expiresAt;

    public String getMessage() {
        return message;
    }

    public String getToken() {
        return token;
    }

    public String getExpiresAt() {
        return expiresAt;
    }
}
