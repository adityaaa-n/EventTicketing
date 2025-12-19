package src.com.eventticketing.dao;

import src.com.eventticketing.config.DatabaseConnection;
import src.com.eventticketing.model.Ticket;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class TicketDAO {

    private Connection conn;

    public TicketDAO() {
        conn = DatabaseConnection.getConnection();
    }

    // BUY: User pesan tiket baru
    public boolean createTicket(Ticket ticket) {
        String sql = "INSERT INTO tickets (user_id, event_id, jumlah, total_harga, status) VALUES (?, ?, ?, ?, ?)";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setInt(1, ticket.getUserId());
            stmt.setInt(2, ticket.getEventId());
            stmt.setInt(3, ticket.getJumlah());
            stmt.setBigDecimal(4, ticket.getTotalHarga());
            stmt.setString(5, ticket.getStatus()); // 'pending'
            
            int rowsInserted = stmt.executeUpdate();
            
            if (rowsInserted > 0) {
                try (ResultSet generatedKeys = stmt.getGeneratedKeys()) {
                    if (generatedKeys.next()) {
                        ticket.setTicketId(generatedKeys.getInt(1));
                    }
                }
                return true;
            }
        } catch (SQLException e) {
            System.err.println("Gagal Buat Tiket: " + e.getMessage());
        }
        return false;
    }

    // READ: Ambil semua tiket milik satu user tertentu
    public List<Ticket> getTicketsByUser(int userId) {
        List<Ticket> tickets = new ArrayList<>();
        String sql = "SELECT * FROM tickets WHERE user_id = ? ORDER BY tanggal_beli DESC";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();
            
            while (rs.next()) {
                Ticket ticket = new Ticket();
                ticket.setTicketId(rs.getInt("ticket_id"));
                ticket.setUserId(rs.getInt("user_id"));
                ticket.setEventId(rs.getInt("event_id"));
                ticket.setJumlah(rs.getInt("jumlah"));
                ticket.setTotalHarga(rs.getBigDecimal("total_harga"));
                ticket.setStatus(rs.getString("status"));
                ticket.setTanggalBeli(rs.getTimestamp("tanggal_beli"));
                
                tickets.add(ticket);
            }
        } catch (SQLException e) {
            System.err.println("Gagal Ambil Tiket User: " + e.getMessage());
        }
        return tickets;
    }

    // UPDATE: Ubah status pembayaran (misal dari 'pending' ke 'paid')
    public boolean updateTicketStatus(int ticketId, String status) {
        String sql = "UPDATE tickets SET status = ? WHERE ticket_id = ?";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setString(1, status);
            stmt.setInt(2, ticketId);
            
            int rowsUpdated = stmt.executeUpdate();
            return rowsUpdated > 0;
        } catch (SQLException e) {
            System.err.println("Gagal Update Status Tiket: " + e.getMessage());
            return false;
        }
    }
}