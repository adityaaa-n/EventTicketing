package src.com.eventticketing.model;

import java.math.BigDecimal;
import java.sql.Timestamp;

public class Ticket {
    private int ticketId;
    private int userId;
    private int eventId;
    private int jumlah;
    private BigDecimal totalHarga;
    private String status; // 'pending', 'paid', 'cancelled'
    private Timestamp tanggalBeli;

    public Ticket() {}

    public Ticket(int ticketId, int userId, int eventId, int jumlah, BigDecimal totalHarga, String status, Timestamp tanggalBeli) {
        this.ticketId = ticketId;
        this.userId = userId;
        this.eventId = eventId;
        this.jumlah = jumlah;
        this.totalHarga = totalHarga;
        this.status = status;
        this.tanggalBeli = tanggalBeli;
    }

    // Constructor untuk insert baru
    public Ticket(int userId, int eventId, int jumlah, BigDecimal totalHarga, String status) {
        this.userId = userId;
        this.eventId = eventId;
        this.jumlah = jumlah;
        this.totalHarga = totalHarga;
        this.status = status;
    }

    // Getter Setter
    public int getTicketId() { return ticketId; }
    public void setTicketId(int ticketId) { this.ticketId = ticketId; }

    public int getUserId() { return userId; }
    public void setUserId(int userId) { this.userId = userId; }

    public int getEventId() { return eventId; }
    public void setEventId(int eventId) { this.eventId = eventId; }

    public int getJumlah() { return jumlah; }
    public void setJumlah(int jumlah) { this.jumlah = jumlah; }

    public BigDecimal getTotalHarga() { return totalHarga; }
    public void setTotalHarga(BigDecimal totalHarga) { this.totalHarga = totalHarga; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public Timestamp getTanggalBeli() { return tanggalBeli; }
    public void setTanggalBeli(Timestamp tanggalBeli) { this.tanggalBeli = tanggalBeli; }
}