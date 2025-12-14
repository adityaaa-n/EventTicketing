package src.com.eventticketing.model;

import java.sql.Timestamp;

public class User {
    private int userId;
    private String nama;
    private String email;
    private String password;
    private Timestamp createdAt;

    // Constructor Kosong (Penting untuk beberapa library/framework)
    public User() {}

    // Constructor Lengkap (Untuk membuat object baru dengan mudah)
    public User(int userId, String nama, String email, String password, Timestamp createdAt) {
        this.userId = userId;
        this.nama = nama;
        this.email = email;
        this.password = password;
        this.createdAt = createdAt;
    }

    // Constructor Tanpa ID dan Timestamp (Untuk Register User Baru - ID auto increment)
    public User(String nama, String email, String password) {
        this.nama = nama;
        this.email = email;
        this.password = password;
    }

    // Getter dan Setter
    public int getUserId() { return userId; }
    public void setUserId(int userId) { this.userId = userId; }

    public String getNama() { return nama; }
    public void setNama(String nama) { this.nama = nama; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getPassword() { return password; }
    public void setPassword(String password) { this.password = password; }

    public Timestamp getCreatedAt() { return createdAt; }
    public void setCreatedAt(Timestamp createdAt) { this.createdAt = createdAt; }
}