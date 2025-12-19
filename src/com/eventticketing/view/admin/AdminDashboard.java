package src.com.eventticketing.view.admin;

import src.com.eventticketing.model.Admin;
import src.com.eventticketing.view.auth.LoginView;
import javax.swing.*;
import java.awt.*;

public class AdminDashboard extends JFrame {

    private Admin currentAdmin;

    public AdminDashboard(Admin admin) {
        this.currentAdmin = admin;

        setTitle("Admin Dashboard - Event Ticketing");
        setSize(600, 400);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // Header
        JLabel lblTitle = new JLabel("Panel Admin: " + admin.getNama(), SwingConstants.CENTER);
        lblTitle.setFont(new Font("Arial", Font.BOLD, 24));
        lblTitle.setBorder(BorderFactory.createEmptyBorder(20, 0, 20, 0));
        add(lblTitle, BorderLayout.NORTH);

        // Menu Tombol
        JPanel panelMenu = new JPanel(new GridLayout(2, 1, 10, 10));
        panelMenu.setBorder(BorderFactory.createEmptyBorder(20, 50, 20, 50));

        JButton btnKelolaEvent = new JButton("Kelola Data Event (CRUD)");
        JButton btnLaporan = new JButton("Lihat Laporan Transaksi"); // Nanti dibuat
        
        btnKelolaEvent.setFont(new Font("Arial", Font.BOLD, 16));
        btnLaporan.setFont(new Font("Arial", Font.BOLD, 16));

        panelMenu.add(btnKelolaEvent);
        panelMenu.add(btnLaporan);
        add(panelMenu, BorderLayout.CENTER);

        // Tombol Logout
        JButton btnLogout = new JButton("Logout");
        add(btnLogout, BorderLayout.SOUTH);
 
        // --- Event Handling ---
        btnKelolaEvent.addActionListener(e -> {
            new ManageEventView(currentAdmin).setVisible(true);
            this.dispose();
        });

        btnLaporan.addActionListener(e -> {
            JOptionPane.showMessageDialog(this, "Fitur Laporan akan segera hadir!");
        });

        btnLogout.addActionListener(e -> {
            new LoginView().setVisible(true);
            this.dispose();
        });
    }
}