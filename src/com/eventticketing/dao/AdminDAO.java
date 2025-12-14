package src.com.eventticketing.dao;

import src.com.eventticketing.config.DatabaseConnection;
import src.com.eventticketing.model.Admin;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class AdminDAO {

    private Connection conn;

    public AdminDAO() {
        conn = DatabaseConnection.getConnection();
    }

    // Login Admin
    public Admin login(String email, String password) {
        Admin admin = null;
        String sql = "SELECT * FROM admins WHERE email = ? AND password = ?";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setString(1, email);
            stmt.setString(2, password);
            
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                admin = new Admin();
                admin.setAdminId(rs.getInt("admin_id"));
                admin.setNama(rs.getString("nama"));
                admin.setEmail(rs.getString("email"));
                admin.setPassword(rs.getString("password"));
                admin.setCreatedAt(rs.getTimestamp("created_at"));
            }
        } catch (SQLException e) {
            System.err.println("Admin Login Error: " + e.getMessage());
        }
        return admin;
    }
}