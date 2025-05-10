package com.uav.notesapp.activities.auth;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

import com.google.gson.Gson;
import com.google.gson.JsonObject;
import com.google.gson.JsonSyntaxException;
import com.uav.notesapp.Application;
import com.uav.notesapp.R;
import com.uav.notesapp.activities.MainActivity;
import com.uav.notesapp.model.requests.auth.LoginRequest;
import com.uav.notesapp.model.response.auth.LoginResponse;
import com.uav.notesapp.service.ApiService;
import com.uav.notesapp.service.TokenManagerService;

import java.io.IOException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LoginActivity extends AppCompatActivity {

    private static final String TAG = "LoginActivity";

    private EditText etEmail, etPassword;
    private TextView errorView;
    private ApiService apiService;
    private TokenManagerService tokenManagerService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        etEmail = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);
        errorView = findViewById(R.id.error);
        Button btnLogin = findViewById(R.id.btnLogin);
        Button btnGoToRegister = findViewById(R.id.btnGoToRegister);

        tokenManagerService = Application.getTokenManagerService();
        apiService = Application.getApiClient().getApiService();

        btnLogin.setOnClickListener(v -> attemptLogin());

        btnGoToRegister.setOnClickListener(v -> {
            Intent i = new Intent(LoginActivity.this, RegisterActivity.class);
            startActivity(i);
        });
    }

    private void attemptLogin() {
        clearErrorMessage();

        String email = etEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();
        if (email.isEmpty() || password.isEmpty()) {
            errorView.setText("Email and password are required");
            errorView.setVisibility(View.VISIBLE);
            return;
        }

        LoginRequest loginRequest = new LoginRequest(email, password);
        apiService.login(loginRequest).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<LoginResponse> call, @NonNull Response<LoginResponse> response) {
                if (!response.isSuccessful() || response.body() == null) {
                    String errorMessage = "Login failed";

                    try (ResponseBody errorBody = response.errorBody()) {
                        if (errorBody == null) {
                            return;
                        }

                        String bodyContent = errorBody.string();
                        updateErrorMessage(bodyContent);
                        errorMessage += ": " + bodyContent;
                    } catch (IOException e) {
                        Log.e(TAG, "Failed to parse error body", e);
                    }

                    Log.e(TAG, errorMessage);
                    return;
                }

                Log.i(TAG, "Login successful:" + response.body().getMessage());
                Toast.makeText(LoginActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();

                tokenManagerService.saveToken(response.body().getToken());

                Intent i = new Intent(LoginActivity.this, MainActivity.class);
                startActivity(i);
            }

            @Override
            public void onFailure(@NonNull Call<LoginResponse> call, @NonNull Throwable throwable) {
                Log.e(TAG, "Login network error: " + throwable.getMessage(), throwable);
                Toast.makeText(LoginActivity.this, "Network error" + throwable.getMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }

    private void clearErrorMessage() {
        errorView.setText("");
        errorView.setVisibility(View.GONE);
    }

    private void updateErrorMessage(String errorBody) {
        try {
            Gson gson = new Gson();
            JsonObject errorJson = gson.fromJson(errorBody, JsonObject.class);

            if (errorJson != null && errorJson.has("error") && !errorJson.get("error").isJsonNull()) {
                errorView.setText(errorJson.get("error").getAsString());
                errorView.setVisibility(View.VISIBLE);
            }
        } catch (JsonSyntaxException | IllegalStateException e) {
            Log.e(TAG, "Failed to parse JSON error body", e);
        }
    }
}
