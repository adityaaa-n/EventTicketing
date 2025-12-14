package src.com.eventticketing.model;

import java.math.BigDecimal;
import java.sql.Timestamp;

public class PaymentLog {
    private int paymentId;
    private int ticketId;
    private String metode;
    private BigDecimal nominal;
    private Timestamp waktuBayar;

    public PaymentLog() {}

    public PaymentLog(int paymentId, int ticketId, String metode, BigDecimal nominal, Timestamp waktuBayar) {
        this.paymentId = paymentId;
        this.ticketId = ticketId;
        this.metode = metode;
        this.nominal = nominal;
        this.waktuBayar = waktuBayar;
    }

    // Constructor untuk insert baru
    public PaymentLog(int ticketId, String metode, BigDecimal nominal) {
        this.ticketId = ticketId;
        this.metode = metode;
        this.nominal = nominal;
    }

    // Getter Setter
    public int getPaymentId() { return paymentId; }
    public void setPaymentId(int paymentId) { this.paymentId = paymentId; }

    public int getTicketId() { return ticketId; }
    public void setTicketId(int ticketId) { this.ticketId = ticketId; }

    public String getMetode() { return metode; }
    public void setMetode(String metode) { this.metode = metode; }

    public BigDecimal getNominal() { return nominal; }
    public void setNominal(BigDecimal nominal) { this.nominal = nominal; }

    public Timestamp getWaktuBayar() { return waktuBayar; }
    public void setWaktuBayar(Timestamp waktuBayar) { this.waktuBayar = waktuBayar; }
}