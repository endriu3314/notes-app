package com.uav.notesapp.model;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class Note {

    private int id;
    private int userId;
    private User user;
    private List<User> authorizedUsers;
    private String title;
    private String content;

    @SerializedName("createdAt")
    private String createdAt;

    @SerializedName("updatedAt")
    private String updatedAt;

    public int getId() {
        return id;
    }

    public int getUserId() {
        return userId;
    }

    public User getUser() {
        return user;
    }

    public List<User> getAuthorizedUsers() {
        return authorizedUsers;
    }

    public String getTitle() {
        return title;
    }

    public String getContent() {
        return content;
    }

    public String getCreatedAt() {
        return createdAt;
    }

    public String getUpdatedAt() {
        return updatedAt;
    }
}
