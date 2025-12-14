package src.com.eventticketing.model;

import java.sql.Timestamp;

public class Admin {
    private int adminId;
    private String nama;
    private String email;
    private String password;
    private Timestamp createdAt;

    public Admin() {}

    public Admin(int adminId, String nama, String email, String password, Timestamp createdAt) {
        this.adminId = adminId;
        this.nama = nama;
        this.email = email;
        this.password = password;
        this.createdAt = createdAt;
    }

    public Admin(String nama, String email, String password) {
        this.nama = nama;
        this.email = email;
        this.password = password;
    }

    // Getter dan Setter
    public int getAdminId() { return adminId; }
    public void setAdminId(int adminId) { this.adminId = adminId; }

    public String getNama() { return nama; }
    public void setNama(String nama) { this.nama = nama; }

    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }

    public String getPassword() { return password; }
    public void setPassword(String password) { this.password = password; }

    public Timestamp getCreatedAt() { return createdAt; }
    public void setCreatedAt(Timestamp createdAt) { this.createdAt = createdAt; }
}