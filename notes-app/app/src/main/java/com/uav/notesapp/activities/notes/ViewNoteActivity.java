package com.uav.notesapp.activities.notes;

import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Patterns;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.uav.notesapp.Application;
import com.uav.notesapp.R;
import com.uav.notesapp.model.Note;
import com.uav.notesapp.model.requests.notes.AuthorizeUserToNoteRequest;
import com.uav.notesapp.model.requests.notes.UpdateNoteRequest;
import com.uav.notesapp.model.response.notes.NoteDeleteResponse;
import com.uav.notesapp.model.response.notes.NoteResponse;
import com.uav.notesapp.model.response.notes.NoteUpdateResponse;
import com.uav.notesapp.service.NoteService;

public class ViewNoteActivity extends AppCompatActivity {

    public static final String NOTE_ID_TAG = "com.uav.notesapp.activities.notes.AppCompatActivity.noteId";

    private NoteService noteService;

    private int noteId;

    private boolean isLoading = false;
    private ProgressBar pbLoading;

    private TextView tvCreatedAt, tvUpdatedAt, tvAuthor, tvError;
    private EditText etTitle, etContent;

    private LinearLayout llAuthorizedUsersList;
    private Button btnSave, btnAddUser, btnDelete;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        initializeView();

        Bundle b = getIntent().getExtras();
        if (b != null) {
            noteId = b.getInt(NOTE_ID_TAG);
        }

        noteService = new NoteService();

        initializeElements();
        initializeButtons();

        fetchNote();
    }

    private void initializeView() {
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_view_note);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });
    }

    private void initializeElements() {
        pbLoading = findViewById(R.id.progressBar);

        tvError = findViewById(R.id.error);
        tvCreatedAt = findViewById(R.id.createdAt);
        tvUpdatedAt = findViewById(R.id.updatedAt);
        tvAuthor = findViewById(R.id.author);
        llAuthorizedUsersList = findViewById(R.id.authorizedUsersList);
        etTitle = findViewById(R.id.title);
        etContent = findViewById(R.id.content);

        etTitle.addTextChangedListener(getWatcherToEnableSaveButton());
        etContent.addTextChangedListener(getWatcherToEnableSaveButton());
    }

    private void initializeButtons() {
        btnSave = findViewById(R.id.updateContentButton);
        btnSave.setOnClickListener(v -> saveNote());
        btnDelete = findViewById(R.id.deleteNoteButton);
        btnDelete.setOnClickListener(v -> deleteNote());
        btnAddUser = findViewById(R.id.authorizeUserButton);
        btnAddUser.setOnClickListener(v -> authorizeUserToNote());
    }

    private TextWatcher getWatcherToEnableSaveButton() {
        return new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                //
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                //
            }

            @Override
            public void afterTextChanged(Editable s) {
                if (etContent.hasFocus() || etTitle.hasFocus()) {
                    btnSave.setEnabled(true);
                }
            }
        };
    }

    private void setIsLoading(boolean status) {
        if (status) {
            isLoading = true;
            pbLoading.setVisibility(View.VISIBLE);
            return;
        }

        isLoading = false;
        pbLoading.setVisibility(View.GONE);
    }

    private void updateNoteUi(@NonNull Note note) {
        this.etTitle.setText(note.getTitle());
        this.tvUpdatedAt.setText("Updated at: " + note.getUpdatedAt());
        this.tvCreatedAt.setText("Created at: " + note.getUpdatedAt());
        this.tvAuthor.setText(note.getUser().getName());
        this.etContent.setText(note.getContent());

        llAuthorizedUsersList.removeAllViews();
        note.getAuthorizedUsers().forEach(user -> {
            Button userButton = new Button(this);

            userButton.setText(user.getName());
            userButton.setId(View.generateViewId());
            userButton.setOnClickListener(v -> unauthorizeUserToNote(user.getId()));

            llAuthorizedUsersList.addView(userButton);
        });

        if (note.getUser().getId() == Application.getAuthUser().getId()) {
            btnDelete.setEnabled(true);
            btnAddUser.setEnabled(true);
        } else {
            btnDelete.setEnabled(false);
            btnAddUser.setEnabled(false);
        }
    }

    private void fetchNote() {
        if (isLoading) {
            return;
        }

        setIsLoading(true);
        noteService.fetchNote(noteId, new NoteService.ResponseListener<NoteResponse>() {
            @Override
            public void onSuccess(NoteResponse response) {
                setIsLoading(false);
                updateNoteUi(response.getData());
            }

            @Override
            public void onFailure(String errorMessage) {
                setIsLoading(false);
                Toast.makeText(ViewNoteActivity.this, errorMessage, Toast.LENGTH_SHORT).show();

            }
        });
    }

    private void saveNote() {
        tvError.setText("");
        tvError.setVisibility(View.GONE);

        String newTitle = etTitle.getText().toString();
        String newContent = etContent.getText().toString();

        if (newTitle.isEmpty() || newContent.isEmpty()) {
            tvError.setText("Title and content are required");
            tvError.setVisibility(View.VISIBLE);
            return;
        }

        setIsLoading(true);
        UpdateNoteRequest request = new UpdateNoteRequest(newTitle, newContent);
        noteService.updateNote(noteId, request, new NoteService.ResponseListener<>() {
            @Override
            public void onSuccess(NoteUpdateResponse response) {
                setIsLoading(false);
                Toast.makeText(ViewNoteActivity.this, response.getMessage(), Toast.LENGTH_LONG).show();
                btnSave.setEnabled(false);
            }

            @Override
            public void onFailure(String errorMessage) {
                setIsLoading(false);
                tvError.setText(errorMessage);
                tvError.setVisibility(View.VISIBLE);
                Toast.makeText(ViewNoteActivity.this, errorMessage, Toast.LENGTH_LONG).show();
            }
        });
    }

    private void deleteNote() {
        setIsLoading(true);
        noteService.deleteNote(noteId, new NoteService.ResponseListener<>() {
            @Override
            public void onSuccess(NoteDeleteResponse response) {
                setIsLoading(false);
                Toast.makeText(ViewNoteActivity.this, response.getMessage(), Toast.LENGTH_SHORT).show();
                Intent i = new Intent(ViewNoteActivity.this, IndexNoteActivity.class);
                startActivity(i);
            }

            @Override
            public void onFailure(String errorMessage) {
                setIsLoading(false);
                Toast.makeText(ViewNoteActivity.this, errorMessage, Toast.LENGTH_LONG).show();

            }
        });
    }

    private void authorizeUserToNote() {
        EditText input = new EditText(this);
        input.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_VARIATION_EMAIL_ADDRESS);
        input.setHint("email@example.com");

        new AlertDialog.Builder(this)
            .setTitle("Add user to note")
            .setMessage("Enter another user email to allow him to view & edit this note.")
            .setView(input)
            .setPositiveButton("Submit", (dialog, which) -> {
                String email = input.getText().toString().trim();

                if (email.isEmpty() || !Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
                    Toast.makeText(ViewNoteActivity.this, "Please enter a valid email address", Toast.LENGTH_SHORT).show();
                    return;
                }

                setIsLoading(true);

                noteService.addUserAuthorizedToNote(noteId, new AuthorizeUserToNoteRequest(email), new NoteService.ResponseListener<>() {
                    @Override
                    public void onSuccess(NoteUpdateResponse response) {
                        setIsLoading(false);
                        Toast.makeText(ViewNoteActivity.this, response.getMessage(), Toast.LENGTH_SHORT).show();
                        fetchNote();
                    }

                    @Override
                    public void onFailure(String errorMessage) {
                        setIsLoading(false);
                        Toast.makeText(ViewNoteActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    }
                });
            })
            .setNegativeButton("Cancel", (dialog, which) -> dialog.cancel())
            .create()
            .show();
    }

    private void unauthorizeUserToNote(int userId) {
        new AlertDialog.Builder(this)
            .setTitle("Confirm")
            .setMessage("Are you sure you want to remove this users access to this note?")
            .setPositiveButton("Submit", (dialog, which) -> {
                setIsLoading(true);

                noteService.removeUserAuthorizedToNote(noteId, userId, new NoteService.ResponseListener<NoteUpdateResponse>() {
                    @Override
                    public void onSuccess(NoteUpdateResponse response) {
                        setIsLoading(false);
                        Toast.makeText(ViewNoteActivity.this, response.getMessage(), Toast.LENGTH_SHORT).show();
                        fetchNote();
                    }

                    @Override
                    public void onFailure(String errorMessage) {
                        setIsLoading(false);
                        Toast.makeText(ViewNoteActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    }
                });
            })
            .setNegativeButton("Cancel", (dialog, which) -> dialog.cancel())
            .create()
            .show();
    }
}
