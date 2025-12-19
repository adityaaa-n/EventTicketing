package src.com.eventticketing.dao;

import src.com.eventticketing.config.DatabaseConnection;
import src.com.eventticketing.model.User;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class UserDAO {

    private Connection conn;

    public UserDAO() {
        conn = DatabaseConnection.getConnection();
    }

    // Fitur 1: Login User (Sesuai SRS Bab II.1)
    public User login(String email, String password) {
        User user = null;
        String sql = "SELECT * FROM users WHERE email = ? AND password = ?";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setString(1, email);
            stmt.setString(2, password);
            
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                user = new User();
                user.setUserId(rs.getInt("user_id"));
                user.setNama(rs.getString("nama"));
                user.setEmail(rs.getString("email"));
                user.setPassword(rs.getString("password"));
                user.setCreatedAt(rs.getTimestamp("created_at"));
            }
        } catch (SQLException e) {
            System.err.println("Login Error: " + e.getMessage());
        }
        return user;
    }

    // Fitur 2: Register User Baru
    public boolean register(User user) {
        String sql = "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setString(1, user.getNama());
            stmt.setString(2, user.getEmail());
            stmt.setString(3, user.getPassword());
            
            int rowsInserted = stmt.executeUpdate();
            return rowsInserted > 0;
        } catch (SQLException e) {
            System.err.println("Register Error: " + e.getMessage());
            return false;
        }
    }
    
    // Fitur tambahan: Cek apakah email sudah terdaftar
    public boolean isEmailExists(String email) {
        String sql = "SELECT user_id FROM users WHERE email = ?";
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setString(1, email);
            ResultSet rs = stmt.executeQuery();
            return rs.next(); // True jika email ditemukan
        } catch (SQLException e) {
            e.printStackTrace(); 
            return false; // Jika terjadi error, anggap email tidak ada
        }
    }
}