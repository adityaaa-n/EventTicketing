package src.com.eventticketing.view.user;

import src.com.eventticketing.model.User;
import src.com.eventticketing.view.auth.LoginView;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;

public class UserDashboard extends JFrame {

    private User currentUser;
    private JTable tableEvents;
    private DefaultTableModel tableModel;

    public UserDashboard(User user) {
        this.currentUser = user;

        // Setup Window
        setTitle("Dashboard User - Event Ticketing ");
        setSize(800, 500);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // --- 1. Header ---
        JPanel panelHeader = new JPanel(new BorderLayout());
        panelHeader.setBorder(BorderFactory.createEmptyBorder(15, 20, 15, 20));
        panelHeader.setBackground(new Color(70, 130, 180));

        JLabel lblWelcome = new JLabel("Halo, " + currentUser.getNama() + " (Mode Demo)");
        lblWelcome.setFont(new Font("Arial", Font.BOLD, 20));
        lblWelcome.setForeground(Color.WHITE);
        
        JButton btnLogout = new JButton("Logout");
        btnLogout.addActionListener(e -> {
            new LoginView().setVisible(true);
            this.dispose();
        });

        panelHeader.add(lblWelcome, BorderLayout.WEST);
        panelHeader.add(btnLogout, BorderLayout.EAST);
        add(panelHeader, BorderLayout.NORTH);

        // --- 2. Tabel Daftar Event ---
        String[] columnNames = {"ID", "Nama Event", "Tanggal", "Lokasi", "Harga (Rp)", "Sisa Kuota"};
        tableModel = new DefaultTableModel(columnNames, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false; 
            }
        };

        tableEvents = new JTable(tableModel);
        tableEvents.setRowHeight(30);
        
        // Panggil fungsi load data dummy
        loadDataEventsDummy();

        JScrollPane scrollPane = new JScrollPane(tableEvents);
        scrollPane.setBorder(BorderFactory.createTitledBorder("Daftar Event"));
        add(scrollPane, BorderLayout.CENTER);

        // --- 3. Panel Tombol Aksi ---
        JPanel panelBottom = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        JButton btnRefresh = new JButton("Refresh Data");
        JButton btnBeli = new JButton("Beli Tiket");
        
        btnBeli.setBackground(new Color(34, 139, 34));
        btnBeli.setForeground(Color.WHITE);
        btnBeli.setFont(new Font("Arial", Font.BOLD, 14));

        panelBottom.add(btnRefresh);
        panelBottom.add(btnBeli);
        add(panelBottom, BorderLayout.SOUTH);

        // --- Event Handling ---
        
        // Tombol Refresh (Reset ke data dummy)
        btnRefresh.addActionListener(e -> {
            loadDataEventsDummy();
            JOptionPane.showMessageDialog(this, "Data direfresh");
        });

        // Tombol Beli (Hanya Tampilan)
        btnBeli.addActionListener(e -> {
            int selectedRow = tableEvents.getSelectedRow();
            if (selectedRow != -1) {
                String namaEvent = (String) tableModel.getValueAt(selectedRow, 1);
                
                // Tampilkan pesan saja
                JOptionPane.showMessageDialog(this, 
                    "Anda memilih event: " + namaEvent + "\n\n" +
                    "Fitur Pembelian Belum Ada",
                    "Info Demo", JOptionPane.INFORMATION_MESSAGE);
                
            } else {
                JOptionPane.showMessageDialog(this, "Pilih salah satu event di tabel dulu!");
            }
        });
    }

    // Method Dummy: Mengisi tabel manual
    private void loadDataEventsDummy() {
        tableModel.setRowCount(0);

        // DATA sementara
        tableModel.addRow(new Object[]{1, "Konser Musik Java", "2025-12-31", "Jakarta", 150000, 100});
        tableModel.addRow(new Object[]{2, "Festival Kuliner", "2025-10-10", "Bandung", 50000, 200});
        tableModel.addRow(new Object[]{3, "Seminar Tech", "2025-08-17", "Surabaya", 75000, 50});
        tableModel.addRow(new Object[]{4, "Pameran Seni", "2025-05-02", "Yogyakarta", 25000, 10});
    }
}