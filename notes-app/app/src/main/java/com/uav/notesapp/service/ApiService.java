package com.uav.notesapp.service;

import com.uav.notesapp.model.requests.auth.LoginRequest;
import com.uav.notesapp.model.requests.auth.RegisterRequest;
import com.uav.notesapp.model.requests.notes.AuthorizeUserToNoteRequest;
import com.uav.notesapp.model.requests.notes.UpdateNoteRequest;
import com.uav.notesapp.model.response.MeResponse;
import com.uav.notesapp.model.response.auth.LoginResponse;
import com.uav.notesapp.model.response.auth.RegisterResponse;
import com.uav.notesapp.model.response.notes.NoteCreateResponse;
import com.uav.notesapp.model.response.notes.NoteDeleteResponse;
import com.uav.notesapp.model.response.notes.NoteResponse;
import com.uav.notesapp.model.response.notes.NoteUpdateResponse;
import com.uav.notesapp.model.response.notes.NotesResponse;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface ApiService {
    @POST("auth/login")
    Call<LoginResponse> login(@Body LoginRequest loginRequest);

    @POST("auth/register")
    Call<RegisterResponse> register(@Body RegisterRequest registerRequest);

    @GET("auth/me")
    Call<MeResponse> getMe();

    @GET("notes")
    Call<NotesResponse> getNotes(@Query("page") int page, @Query("onlyPersonal") boolean onlyPersonal);

    @GET("notes/{id}")
    Call<NoteResponse> getNote(@Path("id") int id);

    @POST("notes")
    Call<NoteCreateResponse> createEmptyNote();

    @PUT("notes/{id}")
    Call<NoteUpdateResponse> updateNote(@Path("id") int id, @Body UpdateNoteRequest updateNoteRequest);

    @DELETE("notes/{id}")
    Call<NoteDeleteResponse> deleteNote(@Path("id") int id);

    @POST("notes/{id}/authorize")
    Call<NoteUpdateResponse> authorizeUserToNote(@Path("id") int id, @Body AuthorizeUserToNoteRequest authorizeUserToNoteRequest);

    @DELETE("notes/{id}/authorize/{userId}")
    Call<NoteUpdateResponse> unauthorizeUserToNote(@Path("id") int id, @Path("userId") int userId);
}
