package com.uav.notesapp.model.response;

import com.uav.notesapp.model.Pagination;

import java.util.List;

public class PaginatedResponse<T> {

    private String message;
    private List<T> data;
    private Pagination pagination;

    public String getMessage() {
        return message;
    }

    public List<T> getData() {
        return data;
    }

    public Pagination getPagination() {
        return pagination;
    }
}
