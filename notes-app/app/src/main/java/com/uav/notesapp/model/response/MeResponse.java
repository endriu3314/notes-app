package com.uav.notesapp.model.response;

import com.uav.notesapp.model.User;

public class MeResponse {
    private String message;
    private User user;

    public String getMessage() {
        return message;
    }

    public User getUser() {
        return user;
    }
}
