package src.com.eventticketing.dao;

import src.com.eventticketing.config.DatabaseConnection;
import src.com.eventticketing.model.Event;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class EventDAO {

    private Connection conn;

    public EventDAO() {
        conn = DatabaseConnection.getConnection();
    }

    // CREATE: Tambah Event Baru
    public boolean addEvent(Event event) {
        String sql = "INSERT INTO events (nama_event, deskripsi, lokasi, tanggal, harga, kuota, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setString(1, event.getNamaEvent());
            stmt.setString(2, event.getDeskripsi());
            stmt.setString(3, event.getLokasi());
            stmt.setDate(4, event.getTanggal());
            stmt.setBigDecimal(5, event.getHarga());
            stmt.setInt(6, event.getKuota());
            stmt.setInt(7, event.getCreatedBy());
            
            int rowsInserted = stmt.executeUpdate();
            return rowsInserted > 0;
        } catch (SQLException e) {
            System.err.println("Gagal Tambah Event: " + e.getMessage());
            return false;
        }
    }

    // READ: Ambil Semua Event (Untuk Tampilan User & Admin)
    public List<Event> getAllEvents() {
        List<Event> events = new ArrayList<>();
        String sql = "SELECT * FROM events ORDER BY tanggal ASC";
        
        try (Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {
            
            while (rs.next()) {
                Event event = new Event();
                event.setEventId(rs.getInt("event_id"));
                event.setNamaEvent(rs.getString("nama_event"));
                event.setDeskripsi(rs.getString("deskripsi"));
                event.setLokasi(rs.getString("lokasi"));
                event.setTanggal(rs.getDate("tanggal"));
                event.setHarga(rs.getBigDecimal("harga"));
                event.setKuota(rs.getInt("kuota"));
                event.setCreatedBy(rs.getInt("created_by"));
                
                events.add(event);
            }
        } catch (SQLException e) {
            System.err.println("Gagal Ambil Data Event: " + e.getMessage());
        }
        return events;
    }

    // READ: Ambil 1 Event berdasarkan ID (Untuk Detail Page)
    public Event getEventById(int eventId) {
        Event event = null;
        String sql = "SELECT * FROM events WHERE event_id = ?";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, eventId);
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                event = new Event(
                    rs.getInt("event_id"),
                    rs.getString("nama_event"),
                    rs.getString("deskripsi"),
                    rs.getString("lokasi"),
                    rs.getDate("tanggal"),
                    rs.getBigDecimal("harga"),
                    rs.getInt("kuota"),
                    rs.getInt("created_by")
                );
            }
        } catch (SQLException e) {
            System.err.println("Error Get Event By ID: " + e.getMessage());
        }
        return event;
    }

    // UPDATE: Edit Data Event
    public boolean updateEvent(Event event) {
        String sql = "UPDATE events SET nama_event=?, deskripsi=?, lokasi=?, tanggal=?, harga=?, kuota=? WHERE event_id=?";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setString(1, event.getNamaEvent());
            stmt.setString(2, event.getDeskripsi());
            stmt.setString(3, event.getLokasi());
            stmt.setDate(4, event.getTanggal());
            stmt.setBigDecimal(5, event.getHarga());
            stmt.setInt(6, event.getKuota());
            stmt.setInt(7, event.getEventId());
            
            int rowsUpdated = stmt.executeUpdate();
            return rowsUpdated > 0;
        } catch (SQLException e) {
            System.err.println("Gagal Update Event: " + e.getMessage());
            return false;
        }
    }

    // DELETE: Hapus Event
    public boolean deleteEvent(int eventId) {
        String sql = "DELETE FROM events WHERE event_id = ?";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, eventId);
            
            int rowsDeleted = stmt.executeUpdate();
            return rowsDeleted > 0;
        } catch (SQLException e) {
            System.err.println("Gagal Hapus Event: " + e.getMessage());
            return false;
        }
    }
}