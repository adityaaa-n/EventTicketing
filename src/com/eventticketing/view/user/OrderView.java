package src.com.eventticketing.view.user;

import src.com.eventticketing.dao.EventDAO;
import src.com.eventticketing.dao.PaymentLogDAO;
import src.com.eventticketing.dao.TicketDAO;
import src.com.eventticketing.model.Event;
import src.com.eventticketing.model.PaymentLog;
import src.com.eventticketing.model.Ticket;
import src.com.eventticketing.model.User;
import javax.swing.*;
import java.awt.*;
import java.math.BigDecimal;

public class OrderView extends JFrame {

    private User currentUser;
    private Event currentEvent;
    
    // Komponen GUI
    private JLabel lblNamaEvent, lblHargaSatuan, lblTotalHarga;
    private JSpinner spinnerJumlah;
    private JComboBox<String> cmbMetodeBayar;
    private JButton btnBayar;

    public OrderView(User user, Event event) {
        this.currentUser = user;
        this.currentEvent = event;

        setTitle("Form Pemesanan Tiket");
        setSize(400, 450);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE); // Hanya tutup jendela ini, bukan aplikasi
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // --- Header ---
        JLabel lblTitle = new JLabel("Konfirmasi Pesanan", SwingConstants.CENTER);
        lblTitle.setFont(new Font("Arial", Font.BOLD, 18));
        lblTitle.setBorder(BorderFactory.createEmptyBorder(15, 0, 15, 0));
        add(lblTitle, BorderLayout.NORTH);

        // --- Form Input ---
        JPanel panelForm = new JPanel(new GridLayout(6, 2, 10, 10));
        panelForm.setBorder(BorderFactory.createEmptyBorder(10, 30, 10, 30));

        panelForm.add(new JLabel("Event:"));
        lblNamaEvent = new JLabel(event.getNamaEvent());
        lblNamaEvent.setFont(new Font("Arial", Font.BOLD, 14));
        panelForm.add(lblNamaEvent);

        panelForm.add(new JLabel("Harga Tiket:"));
        lblHargaSatuan = new JLabel("Rp " + event.getHarga());
        panelForm.add(lblHargaSatuan);

        panelForm.add(new JLabel("Jumlah Beli:"));
        // Spinner angka 1 sampai sisa kuota
        SpinnerModel model = new SpinnerNumberModel(1, 1, event.getKuota(), 1);
        spinnerJumlah = new JSpinner(model);
        panelForm.add(spinnerJumlah);

        panelForm.add(new JLabel("Metode Bayar:"));
        String[] metode = {"Transfer Bank (BCA)", "E-Wallet (GoPay/OVO)", "Kartu Kredit"};
        cmbMetodeBayar = new JComboBox<>(metode);
        panelForm.add(cmbMetodeBayar);

        panelForm.add(new JLabel("TOTAL BAYAR:"));
        lblTotalHarga = new JLabel("Rp " + event.getHarga()); // Default harga 1 tiket
        lblTotalHarga.setFont(new Font("Arial", Font.BOLD, 16));
        lblTotalHarga.setForeground(new Color(0, 100, 0)); // Warna Hijau Gelap
        panelForm.add(lblTotalHarga);

        add(panelForm, BorderLayout.CENTER);

        // --- Tombol Aksi ---
        btnBayar = new JButton("Bayar Sekarang");
        btnBayar.setBackground(new Color(34, 139, 34)); // Hijau
        btnBayar.setForeground(Color.WHITE);
        btnBayar.setFont(new Font("Arial", Font.BOLD, 14));
        btnBayar.setPreferredSize(new Dimension(0, 50));
        
        add(btnBayar, BorderLayout.SOUTH);

        // --- LOGIKA PROGRAM ---

        // 1. Update Total Harga saat Jumlah diubah
        spinnerJumlah.addChangeListener(e -> {
            hitungTotal();
        });

        // 2. Klik Tombol Bayar
        btnBayar.addActionListener(e -> prosesPembayaran());
    }

    private void hitungTotal() {
        int jumlah = (int) spinnerJumlah.getValue();
        BigDecimal hargaSatuan = currentEvent.getHarga();
        BigDecimal total = hargaSatuan.multiply(new BigDecimal(jumlah));
        
        lblTotalHarga.setText("Rp " + total);
    }

    private void prosesPembayaran() {
        int jumlah = (int) spinnerJumlah.getValue();
        BigDecimal totalHarga = currentEvent.getHarga().multiply(new BigDecimal(jumlah));
        String metode = (String) cmbMetodeBayar.getSelectedItem();

        // 1. Validasi Kuota Terakhir (Cek lagi takutnya keduluan orang lain)
        if (jumlah > currentEvent.getKuota()) {
            JOptionPane.showMessageDialog(this, "Stok tiket tidak cukup!");
            return;
        }

        // 2. Simpan ke Tabel TICKETS
        TicketDAO ticketDAO = new TicketDAO();
        Ticket newTicket = new Ticket();
        newTicket.setUserId(currentUser.getUserId());
        newTicket.setEventId(currentEvent.getEventId());
        newTicket.setJumlah(jumlah);
        newTicket.setTotalHarga(totalHarga);
        newTicket.setStatus("paid"); // Anggap langsung lunas (Simulasi)

        if (ticketDAO.createTicket(newTicket)) {
            // Jika berhasil simpan tiket, lanjut simpan log pembayaran
            
            // 3. Simpan ke Tabel PAYMENT_LOGS
            PaymentLogDAO paymentDAO = new PaymentLogDAO();
            // ID Tiket otomatis terisi di object newTicket setelah insert berhasil
            PaymentLog log = new PaymentLog(newTicket.getTicketId(), metode, totalHarga);
            paymentDAO.createPaymentLog(log);

            // 4. Update Kuota Event (Kurangi stok di database)
            EventDAO eventDAO = new EventDAO();
            currentEvent.setKuota(currentEvent.getKuota() - jumlah);
            eventDAO.updateEvent(currentEvent);

            JOptionPane.showMessageDialog(this, "Pembayaran Berhasil!\nTiket Anda telah terbit.");
            this.dispose(); // Tutup jendela order
        } else {
            JOptionPane.showMessageDialog(this, "Gagal memproses transaksi.", "Error", JOptionPane.ERROR_MESSAGE);
        }
    }
}