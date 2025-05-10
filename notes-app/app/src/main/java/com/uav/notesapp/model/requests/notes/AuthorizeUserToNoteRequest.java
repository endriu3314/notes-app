package com.uav.notesapp.model.requests.notes;

public class AuthorizeUserToNoteRequest {

    private final String email;

    public AuthorizeUserToNoteRequest(String email) {
        this.email = email;
    }

    public String getEmail() {
        return email;
    }
}
