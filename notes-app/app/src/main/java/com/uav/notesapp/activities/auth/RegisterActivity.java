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
import com.uav.notesapp.model.requests.auth.RegisterRequest;
import com.uav.notesapp.model.response.auth.RegisterResponse;
import com.uav.notesapp.service.ApiService;

import java.io.IOException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class RegisterActivity extends AppCompatActivity {
    private static final String TAG = "RegisterActivity";

    private EditText etName, etEmail, etPassword;
    private TextView errorView;
    private ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        etName = findViewById(R.id.etName);
        etEmail = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);
        errorView = findViewById(R.id.error);
        Button btnRegister = findViewById(R.id.btnRegister);

        apiService = Application.getApiClient().getApiService();

        btnRegister.setOnClickListener(v -> attemptRegister());
    }

    private void attemptRegister() {
        clearErrorMessage();

        String name = etName.getText().toString().trim();
        String email = etEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();

        if (name.isEmpty() || email.isEmpty() || password.isEmpty()) {
            errorView.setText("Completeaza fieldurile.");
            errorView.setVisibility(View.VISIBLE);
            return;
        }

        RegisterRequest registerRequest = new RegisterRequest(name, email, password);
        apiService.register(registerRequest).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<RegisterResponse> call, @NonNull Response<RegisterResponse> response) {
                if (!response.isSuccessful() || response.body() == null) {
                    String errorMessage = "Register failed";

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

                Log.i(TAG, "Register successful:" + response.body().getMessage());
                Toast.makeText(RegisterActivity.this, response.body().getMessage(), Toast.LENGTH_SHORT).show();

                Intent i = new Intent(RegisterActivity.this, LoginActivity.class);
                startActivity(i);
            }

            @Override
            public void onFailure(@NonNull Call<RegisterResponse> call, @NonNull Throwable throwable) {
                Log.e(TAG, "Login network error: " + throwable.getMessage(), throwable);
                Toast.makeText(RegisterActivity.this, "Network error" + throwable.getMessage(), Toast.LENGTH_LONG).show();
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