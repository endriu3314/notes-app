package com.uav.notesapp.activities;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

import com.uav.notesapp.Application;
import com.uav.notesapp.R;
import com.uav.notesapp.activities.auth.LoginActivity;
import com.uav.notesapp.activities.notes.IndexNoteActivity;
import com.uav.notesapp.model.response.MeResponse;
import com.uav.notesapp.service.ApiService;

import java.io.IOException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MainActivity extends AppCompatActivity {

    private static final String TAG = "MainActivity";

    private ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        apiService = Application.getApiClient().getApiService();

        fetchUserData();
    }

    private void fetchUserData() {
        apiService.getMe().enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<MeResponse> call, @NonNull Response<MeResponse> response) {
                if (!response.isSuccessful() || response.body() == null) {
                    String errorMessage = "Me failed";

                    try (ResponseBody errorBody = response.errorBody()) {
                        if (errorBody == null) {
                            return;
                        }

                        errorMessage += ": " + errorBody.string();
                    } catch (IOException e) {
                        Log.e(TAG, "Failed to parse err body", e);
                    }

                    Log.e(TAG, errorMessage);
                    Intent i = new Intent(MainActivity.this, LoginActivity.class);
                    startActivity(i);
                    return;
                }

                Toast.makeText(MainActivity.this, "Welcome back, " + response.body().getUser().getName(), Toast.LENGTH_LONG).show();

                Application.setAuthUser(response.body().getUser());

                Intent i = new Intent(MainActivity.this, IndexNoteActivity.class);
                startActivity(i);
            }

            @Override
            public void onFailure(@NonNull Call<MeResponse> call, @NonNull Throwable throwable) {
                Log.e(TAG, "Network error: " + throwable.getMessage(), throwable);
                Toast.makeText(MainActivity.this, "Network error" + throwable.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}
