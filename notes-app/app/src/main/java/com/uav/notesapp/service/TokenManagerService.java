package com.uav.notesapp.service;

import android.content.Context;
import android.content.SharedPreferences;

import androidx.annotation.NonNull;

public class TokenManagerService {

    private static final String PREFS_NAME = "NotesAppPrefs";
    private static final String AUTH_TOKEN_KEY = "authToken";

    private final SharedPreferences sharedPreferences;

    public TokenManagerService(@NonNull Context context) {
        sharedPreferences = context.getApplicationContext().getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE);
    }

    public void saveToken(String token) {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString(AUTH_TOKEN_KEY, token);
        editor.apply();
    }

    public String getToken() {
        return sharedPreferences.getString(AUTH_TOKEN_KEY, null);
    }

    public void clearToken() {
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.remove(AUTH_TOKEN_KEY);
        editor.apply();
    }
}
