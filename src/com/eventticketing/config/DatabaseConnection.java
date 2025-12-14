package src.com.eventticketing.config;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DatabaseConnection {
    
    // Pakai MySQL JDBC Driver
    private static final String URL = "jdbc:mysql://localhost:3306/db_event_ticketing";
    
    private static final String USER = "root";     
    private static final String PASSWORD = "";     

    private static Connection connection = null;

    public static Connection getConnection() {
        if (connection == null) {
            try {
                Class.forName("com.mysql.cj.jdbc.Driver");
                
                // Buat Koneksi
                connection = DriverManager.getConnection(URL, USER, PASSWORD);
                System.out.println("Koneksi ke Database BERHASIL! (Pakai MySQL Driver)");
                
            } catch (ClassNotFoundException | SQLException e) {
                System.err.println("Koneksi ke Database GAGAL: " + e.getMessage());
            }
        }
        return connection;
    }
}