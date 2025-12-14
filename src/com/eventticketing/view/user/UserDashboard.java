package src.com.eventticketing.view.user;

import src.com.eventticketing.dao.EventDAO;
import src.com.eventticketing.model.Event;
import src.com.eventticketing.model.User;
import src.com.eventticketing.view.auth.LoginView;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.util.List;

public class UserDashboard extends JFrame {

    private User currentUser; // Menyimpan data user yang sedang login
    private JTable tableEvents;
    private DefaultTableModel tableModel;
    private EventDAO eventDAO;

    public UserDashboard(User user) {
        this.currentUser = user;
        this.eventDAO = new EventDAO();

        // Setup Window
        setTitle("Dashboard User - Event Ticketing");
        setSize(800, 500);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // --- 1. Header (Sapaan User) ---
        JPanel panelHeader = new JPanel(new BorderLayout());
        panelHeader.setBorder(BorderFactory.createEmptyBorder(15, 20, 15, 20));
        panelHeader.setBackground(new Color(70, 130, 180)); // Warna biru baja

        JLabel lblWelcome = new JLabel("Halo, " + currentUser.getNama() + "!");
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
        // Judul Kolom Tabel
        String[] columnNames = {"ID", "Nama Event", "Tanggal", "Lokasi", "Harga (Rp)", "Sisa Kuota"};
        tableModel = new DefaultTableModel(columnNames, 0) {
            // Agar sel tabel tidak bisa diedit langsung
            @Override
            public boolean isCellEditable(int row, int column) {
                return false; 
            }
        };

        tableEvents = new JTable(tableModel);
        tableEvents.setRowHeight(30);
        
        // Load data dari database saat aplikasi dibuka
        loadDataEvents();

        JScrollPane scrollPane = new JScrollPane(tableEvents);
        scrollPane.setBorder(BorderFactory.createTitledBorder("Daftar Event Tersedia"));
        add(scrollPane, BorderLayout.CENTER);

        // --- 3. Panel Tombol Aksi (Beli Tiket) ---
        JPanel panelBottom = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        JButton btnRefresh = new JButton("Refresh Data");
        JButton btnBeli = new JButton("Beli Tiket");
        
        // Percantik Tombol Beli
        btnBeli.setBackground(new Color(34, 139, 34)); // Hijau
        btnBeli.setForeground(Color.WHITE);
        btnBeli.setFont(new Font("Arial", Font.BOLD, 14));

        panelBottom.add(btnRefresh);
        panelBottom.add(btnBeli);
        add(panelBottom, BorderLayout.SOUTH);

        // --- Event Handling Tombol ---
        
        // Tombol Refresh
        btnRefresh.addActionListener(e -> loadDataEvents());

        // Tombol Beli (Logika Sederhana)
        btnBeli.addActionListener(e -> {
            int selectedRow = tableEvents.getSelectedRow();
            if (selectedRow != -1) {
                // Ambil ID Event dari kolom ke-0
                int eventId = (int) tableModel.getValueAt(selectedRow, 0);
                String namaEvent = (String) tableModel.getValueAt(selectedRow, 1);
                
                JOptionPane.showMessageDialog(this, 
                    "Anda memilih event: " + namaEvent + "\n(Fitur Pembayaran akan kita buat selanjutnya!)");
                // Nanti kita arahkan ke TicketDetailView di sini
            } else {
                JOptionPane.showMessageDialog(this, "Pilih salah satu event di tabel dulu!");
            }
        });
    }

    // Method untuk mengambil data dari Database dan memasukkannya ke Tabel
    private void loadDataEvents() {
        // Kosongkan tabel dulu
        tableModel.setRowCount(0);

        // Ambil list event dari DAO
        List<Event> listEvents = eventDAO.getAllEvents();

        // Masukkan ke tabel baris per baris
        for (Event ev : listEvents) {
            Object[] rowData = {
                ev.getEventId(),
                ev.getNamaEvent(),
                ev.getTanggal(),
                ev.getLokasi(),
                ev.getHarga(),
                ev.getKuota()
            };
            tableModel.addRow(rowData);
        }
    }
}