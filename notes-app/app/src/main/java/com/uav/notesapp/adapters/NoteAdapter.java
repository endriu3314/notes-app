package com.uav.notesapp.adapters;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import com.uav.notesapp.R;
import com.uav.notesapp.model.Note;
import java.util.List;

public class NoteAdapter extends ArrayAdapter<Note> {

    private final Context mContext;
    private final int mResource;

    public NoteAdapter(@NonNull Context context, int resource, @NonNull List<Note> objects) {
        super(context, resource, objects);
        mContext = context;
        mResource = resource;
    }

    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent) {
        View v = convertView;
        if (v == null) {
            LayoutInflater vi;
            vi = LayoutInflater.from(mContext);
            v = vi.inflate(mResource, null);
        }

        Note n = getItem(position);

        if (n == null) {
            return v;
        }

        TextView tvName = v.findViewById(R.id.textViewNoteName);
        TextView tvAuthor = v.findViewById(R.id.textViewNoteAuthor);
        TextView tvUpdatedAt = v.findViewById(R.id.textViewNoteUpdatedAt);

        if (tvName != null) {
            tvName.setText(n.getTitle());
        }

        if (tvAuthor != null) {
            tvAuthor.setText(n.getUser().getName());
        }

        if (tvUpdatedAt != null) {
            tvUpdatedAt.setText(n.getUpdatedAt());
        }

        return v;
    }
}
