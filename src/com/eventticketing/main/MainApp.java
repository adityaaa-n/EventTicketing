package src.com.eventticketing.main;

import src.com.eventticketing.config.DatabaseConnection;

public class MainApp {
    public static void main(String[] args) {
        // Panggil method koneksi untuk mengetes
        DatabaseConnection.getConnection();
    }
}