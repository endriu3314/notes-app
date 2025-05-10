package com.uav.notesapp.service;

import android.util.Log;

import androidx.annotation.NonNull;

import com.uav.notesapp.Application;
import com.uav.notesapp.model.requests.notes.AuthorizeUserToNoteRequest;
import com.uav.notesapp.model.requests.notes.UpdateNoteRequest;
import com.uav.notesapp.model.response.notes.NoteCreateResponse;
import com.uav.notesapp.model.response.notes.NoteDeleteResponse;
import com.uav.notesapp.model.response.notes.NoteResponse;
import com.uav.notesapp.model.response.notes.NoteUpdateResponse;
import com.uav.notesapp.model.response.notes.NotesResponse;

import java.io.IOException;

import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class NoteService {

    private static final String TAG = "NoteService";

    private final ApiService apiService;

    public NoteService() {
        apiService = Application.getApiClient().getApiService();
    }

    public interface ResponseListener<T> {
        void onSuccess(T response);

        void onFailure(String errorMessage);
    }

    private <T> void handleOnResponse(@NonNull Call<T> call, @NonNull Response<T> response, final ResponseListener<T> listener) {
        if (response.isSuccessful() && response.body() != null) {
            listener.onSuccess(response.body());
            return;
        }

        String errorMessage = "Authorization failed";

        try (ResponseBody errorBody = response.errorBody()) {
            if (errorBody == null) {
                return;
            } else {
                errorMessage += errorBody.string();
            }
        } catch (IOException e) {
            Log.e(TAG, "Failed to parse error body", e);
        }

        listener.onFailure(errorMessage);
    }

    private <T> void handleOnFailure(@NonNull Call<T> call, @NonNull Throwable throwable, final ResponseListener<T> listener) {
        Log.e(TAG, "Network error: " + throwable.getMessage(), throwable);
        listener.onFailure("Network error: " + throwable.getMessage());
    }

    public void fetchNotes(int page, boolean filterByPersonalOnly, final ResponseListener<NotesResponse> listener) {
        apiService.getNotes(page, filterByPersonalOnly).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<NotesResponse> call, @NonNull Response<NotesResponse> response) {
                handleOnResponse(call, response, listener);
            }

            @Override
            public void onFailure(@NonNull Call<NotesResponse> call, @NonNull Throwable throwable) {
                handleOnFailure(call, throwable, listener);
            }
        });
    }

    public void createEmptyNote(final ResponseListener<NoteCreateResponse> listener) {
        apiService.createEmptyNote().enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<NoteCreateResponse> call, @NonNull Response<NoteCreateResponse> response) {
                handleOnResponse(call, response, listener);
            }

            @Override
            public void onFailure(@NonNull Call<NoteCreateResponse> call, @NonNull Throwable throwable) {
                handleOnFailure(call, throwable, listener);
            }
        });
    }

    public void fetchNote(int noteId, final ResponseListener<NoteResponse> listener) {
        apiService.getNote(noteId).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<NoteResponse> call, @NonNull Response<NoteResponse> response) {
                handleOnResponse(call, response, listener);
            }

            @Override
            public void onFailure(@NonNull Call<NoteResponse> call, @NonNull Throwable throwable) {
                handleOnFailure(call, throwable, listener);
            }
        });
    }

    public void updateNote(int noteId, UpdateNoteRequest request, final ResponseListener<NoteUpdateResponse> listener) {
        apiService.updateNote(noteId, request).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<NoteUpdateResponse> call, @NonNull Response<NoteUpdateResponse> response) {
                handleOnResponse(call, response, listener);
            }

            @Override
            public void onFailure(@NonNull Call<NoteUpdateResponse> call, @NonNull Throwable throwable) {
                handleOnFailure(call, throwable, listener);
            }
        });
    }

    public void deleteNote(int noteId, final ResponseListener<NoteDeleteResponse> listener) {
        apiService.deleteNote(noteId).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<NoteDeleteResponse> call, @NonNull Response<NoteDeleteResponse> response) {
                handleOnResponse(call, response, listener);

            }

            @Override
            public void onFailure(@NonNull Call<NoteDeleteResponse> call, @NonNull Throwable throwable) {
                handleOnFailure(call, throwable, listener);

            }
        });
    }

    public void addUserAuthorizedToNote(int noteId, AuthorizeUserToNoteRequest request, final ResponseListener<NoteUpdateResponse> listener) {
        apiService.authorizeUserToNote(noteId, request).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<NoteUpdateResponse> call, @NonNull Response<NoteUpdateResponse> response) {
                handleOnResponse(call, response, listener);
            }

            @Override
            public void onFailure(@NonNull Call<NoteUpdateResponse> call, @NonNull Throwable throwable) {
                handleOnFailure(call, throwable, listener);
            }
        });
    }

    public void removeUserAuthorizedToNote(int noteId, int userId, final ResponseListener<NoteUpdateResponse> listener) {
        apiService.unauthorizeUserToNote(noteId, userId).enqueue(new Callback<>() {
            @Override
            public void onResponse(@NonNull Call<NoteUpdateResponse> call, @NonNull Response<NoteUpdateResponse> response) {
                handleOnResponse(call, response, listener);
            }

            @Override
            public void onFailure(@NonNull Call<NoteUpdateResponse> call, @NonNull Throwable throwable) {
                handleOnFailure(call, throwable, listener);
            }
        });
    }
}
