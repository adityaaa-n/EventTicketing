package src.com.eventticketing.view.user;

import src.com.eventticketing.model.Event;
import src.com.eventticketing.model.Ticket;
import src.com.eventticketing.model.User;
import javax.swing.*;
import java.awt.*;

public class TicketDetailView extends JFrame {

    public TicketDetailView(User user, Ticket ticket, Event event) {
        setTitle("E-Ticket Detail");
        setSize(400, 550);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // --- Header Tiket (Warna Biru) ---
        JPanel panelHeader = new JPanel();
        panelHeader.setBackground(new Color(70, 130, 180));
        panelHeader.setPreferredSize(new Dimension(400, 80));
        panelHeader.setLayout(new GridBagLayout()); // Biar teks di tengah
        
        JLabel lblTitle = new JLabel("E-TICKET");
        lblTitle.setFont(new Font("Arial", Font.BOLD, 24));
        lblTitle.setForeground(Color.WHITE);
        panelHeader.add(lblTitle);
        
        add(panelHeader, BorderLayout.NORTH);

        // --- Isi Tiket (Detail) ---
        JPanel panelContent = new JPanel(new GridLayout(0, 1, 10, 10));
        panelContent.setBorder(BorderFactory.createEmptyBorder(20, 40, 20, 40));
        panelContent.setBackground(Color.WHITE);

        // Helper method untuk menambah baris info
        addInfo(panelContent, "ID TIKET", "#" + ticket.getTicketId());
        addInfo(panelContent, "NAMA EVENT", event.getNamaEvent());
        addInfo(panelContent, "TANGGAL", event.getTanggal().toString());
        addInfo(panelContent, "LOKASI", event.getLokasi());
        addInfo(panelContent, "PEMILIK TIKET", user.getNama());
        addInfo(panelContent, "JUMLAH TIKET", ticket.getJumlah() + " Orang");
        addInfo(panelContent, "STATUS PEMBAYARAN", ticket.getStatus().toUpperCase());
        
        JLabel lblTotal = new JLabel("TOTAL: Rp " + ticket.getTotalHarga());
        lblTotal.setFont(new Font("Arial", Font.BOLD, 18));
        lblTotal.setForeground(new Color(34, 139, 34)); // Hijau
        lblTotal.setHorizontalAlignment(SwingConstants.CENTER);
        lblTotal.setBorder(BorderFactory.createEmptyBorder(10, 0, 0, 0));
        panelContent.add(lblTotal);

        add(panelContent, BorderLayout.CENTER);

        // --- Tombol Tutup ---
        JButton btnClose = new JButton("Tutup / Print");
        btnClose.setPreferredSize(new Dimension(400, 50));
        btnClose.addActionListener(e -> this.dispose());
        add(btnClose, BorderLayout.SOUTH);
    }

    // Fungsi kecil untuk merapikan layout label
    private void addInfo(JPanel panel, String label, String value) {
        JLabel lblKey = new JLabel(label);
        lblKey.setFont(new Font("Arial", Font.PLAIN, 12));
        lblKey.setForeground(Color.GRAY);
        
        JLabel lblValue = new JLabel(value);
        lblValue.setFont(new Font("Arial", Font.BOLD, 14));
        
        panel.add(lblKey);
        panel.add(lblValue);
        panel.add(new JSeparator()); // Garis pembatas tipis
    }
}