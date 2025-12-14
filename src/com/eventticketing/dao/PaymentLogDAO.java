package src.com.eventticketing.dao;

import src.com.eventticketing.config.DatabaseConnection;
import src.com.eventticketing.model.PaymentLog;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.SQLException;

public class PaymentLogDAO {

    private Connection conn;

    public PaymentLogDAO() {
        conn = DatabaseConnection.getConnection();
    }

    // Catat Pembayaran Baru
    public boolean createPaymentLog(PaymentLog log) {
        String sql = "INSERT INTO payment_logs (ticket_id, metode, nominal) VALUES (?, ?, ?)";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, log.getTicketId());
            stmt.setString(2, log.getMetode());
            stmt.setBigDecimal(3, log.getNominal());
            
            int rowsInserted = stmt.executeUpdate();
            return rowsInserted > 0;
        } catch (SQLException e) {
            System.err.println("Gagal Catat Pembayaran: " + e.getMessage());
            return false;
        }
    }
}