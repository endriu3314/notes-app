package com.uav.notesapp.model;

public class Pagination {

    private int currentPage;
    private int perPage;
    private int total;
    private int totalPages;
    private boolean hasNextPage;
    private boolean hasPreviousPage;

    public int getCurrentPage() {
        return currentPage;
    }

    public int getPerPage() {
        return perPage;
    }

    public int getTotal() {
        return total;
    }

    public int getTotalPages() {
        return totalPages;
    }

    public boolean getHasNextPage() {
        return hasNextPage;
    }

    public boolean getHasPreviousPage() {
        return hasPreviousPage;
    }
}
