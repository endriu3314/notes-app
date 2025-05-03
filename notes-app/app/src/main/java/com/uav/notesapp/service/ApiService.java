package com.uav.notesapp.service;

import com.uav.notesapp.model.requests.auth.LoginRequest;
import com.uav.notesapp.model.requests.auth.RegisterRequest;
import com.uav.notesapp.model.response.MeResponse;
import com.uav.notesapp.model.response.auth.LoginResponse;
import com.uav.notesapp.model.response.auth.RegisterResponse;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;

public interface ApiService {
    @POST("auth/login")
    Call<LoginResponse> login(@Body LoginRequest loginRequest);

    @POST("auth/register")
    Call<RegisterResponse> register(@Body RegisterRequest registerRequest);

    @GET("auth/me")
    Call<MeResponse> getMe();
}
