package com.uav.notesapp.activities.notes;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.AbsListView;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.uav.notesapp.R;
import com.uav.notesapp.adapters.NoteAdapter;
import com.uav.notesapp.model.Note;
import com.uav.notesapp.model.response.notes.NoteCreateResponse;
import com.uav.notesapp.model.response.notes.NotesResponse;
import com.uav.notesapp.service.NoteService;

import java.util.ArrayList;
import java.util.List;

public class IndexNoteActivity extends AppCompatActivity {

    private List<Note> notes;
    private NoteAdapter notesAdapter;
    private NoteService noteService;

    private boolean isLoading = false;
    private ProgressBar pbLoading;

    private CheckBox cbPersonalOnly;

    private boolean hasNextPage = false;
    private int currentPage = 1;
    private static final int PAGE_SIZE = 10;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        initializeView();

        noteService = new NoteService();

        pbLoading = findViewById(R.id.progressBar);

        initializeNewNoteButton();
        initializeFilters();
        initializeList();

        fetchNotes();
    }

    private void initializeView() {
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_index_note);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });
    }

    private void initializeNewNoteButton() {
        Button createNoteButton = findViewById(R.id.createButton);
        createNoteButton.setOnClickListener(v -> createEmptyNote());
    }

    private void initializeFilters() {
        cbPersonalOnly = findViewById(R.id.checkboxShowPersonal);
        cbPersonalOnly.setOnCheckedChangeListener((buttonView, isChecked) -> {
            currentPage = 1;
            notes.clear();
            fetchNotes();
        });
    }

    private void initializeList() {
        notes = new ArrayList<>();
        ListView lvNotes = findViewById(R.id.notesListView);
        notesAdapter = new NoteAdapter(this, R.layout.list_item_note, notes);
        lvNotes.setAdapter(notesAdapter);

        lvNotes.setOnScrollListener(new AbsListView.OnScrollListener() {
            @Override
            public void onScrollStateChanged(AbsListView view, int scrollState) {
            }

            @Override
            public void onScroll(AbsListView view, int firstVisibleItem, int visibleItemCount, int totalItemCount) {
                if (!hasNextPage) {
                    return;
                }

                if (isLoading) {
                    return;
                }

                if ((visibleItemCount + firstVisibleItem) >= totalItemCount && firstVisibleItem >= 0 && totalItemCount >= PAGE_SIZE) {
                    currentPage += 1;
                    fetchNotes();
                }
            }
        });

        lvNotes.setOnItemClickListener((parent, view, position, id) -> {
            Note clickedItem = notes.get(position);

            Intent i = new Intent(IndexNoteActivity.this, ViewNoteActivity.class);
            i.putExtra(ViewNoteActivity.NOTE_ID_TAG, clickedItem.getId());
            startActivity(i);
        });
    }

    private void fetchNotes() {
        if (isLoading) {
            return;
        }

        setIsLoading(true);
        noteService.fetchNotes(currentPage, cbPersonalOnly.isChecked(), new NoteService.ResponseListener<>() {
            @Override
            public void onSuccess(NotesResponse response) {
                setIsLoading(false);

                notes.addAll(response.getData());
                notesAdapter.notifyDataSetChanged();

                hasNextPage = response.getPagination().getHasNextPage();
            }

            @Override
            public void onFailure(String errorMessage) {
                setIsLoading(false);
                Toast.makeText(IndexNoteActivity.this, errorMessage, Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void createEmptyNote() {
        if (isLoading) {
            return;
        }

        setIsLoading(true);

        noteService.createEmptyNote(new NoteService.ResponseListener<NoteCreateResponse>() {
            @Override
            public void onSuccess(NoteCreateResponse response) {
                setIsLoading(false);

                currentPage = 1;
                notes.clear();
                fetchNotes();
            }

            @Override
            public void onFailure(String errorMessage) {
                setIsLoading(false);

                Toast.makeText(IndexNoteActivity.this, errorMessage, Toast.LENGTH_SHORT).show();

            }
        });
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
}
