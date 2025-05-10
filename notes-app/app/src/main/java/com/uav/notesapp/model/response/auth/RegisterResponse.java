package com.uav.notesapp.model.response.auth;

import com.uav.notesapp.model.User;

public class RegisterResponse {

    private String message;
    private User user;

    public String getMessage() {
        return message;
    }

    public User getUser() {
        return user;
    }
}
