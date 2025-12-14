package src.com.eventticketing.model;

import java.math.BigDecimal;
import java.sql.Date; // Khusus untuk tipe data DATE di database

public class Event {
    private int eventId;
    private String namaEvent;
    private String deskripsi;
    private String lokasi;
    private Date tanggal;
    private BigDecimal harga;
    private int kuota;
    private int createdBy; // Menyimpan ID Admin pembuat

    public Event() {}

    public Event(int eventId, String namaEvent, String deskripsi, String lokasi, Date tanggal, BigDecimal harga, int kuota, int createdBy) {
        this.eventId = eventId;
        this.namaEvent = namaEvent;
        this.deskripsi = deskripsi;
        this.lokasi = lokasi;
        this.tanggal = tanggal;
        this.harga = harga;
        this.kuota = kuota;
        this.createdBy = createdBy;
    }

    // Constructor untuk insert baru (tanpa ID)
    public Event(String namaEvent, String deskripsi, String lokasi, Date tanggal, BigDecimal harga, int kuota, int createdBy) {
        this.namaEvent = namaEvent;
        this.deskripsi = deskripsi;
        this.lokasi = lokasi;
        this.tanggal = tanggal;
        this.harga = harga;
        this.kuota = kuota;
        this.createdBy = createdBy;
    }

    // Getter Setter
    public int getEventId() { return eventId; }
    public void setEventId(int eventId) { this.eventId = eventId; }

    public String getNamaEvent() { return namaEvent; }
    public void setNamaEvent(String namaEvent) { this.namaEvent = namaEvent; }

    public String getDeskripsi() { return deskripsi; }
    public void setDeskripsi(String deskripsi) { this.deskripsi = deskripsi; }

    public String getLokasi() { return lokasi; }
    public void setLokasi(String lokasi) { this.lokasi = lokasi; }

    public Date getTanggal() { return tanggal; }
    public void setTanggal(Date tanggal) { this.tanggal = tanggal; }

    public BigDecimal getHarga() { return harga; }
    public void setHarga(BigDecimal harga) { this.harga = harga; }

    public int getKuota() { return kuota; }
    public void setKuota(int kuota) { this.kuota = kuota; }

    public int getCreatedBy() { return createdBy; }
    public void setCreatedBy(int createdBy) { this.createdBy = createdBy; }
}