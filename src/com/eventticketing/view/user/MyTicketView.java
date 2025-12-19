package src.com.eventticketing.view.user;

import src.com.eventticketing.dao.EventDAO;
import src.com.eventticketing.dao.TicketDAO;
import src.com.eventticketing.model.Event;
import src.com.eventticketing.model.Ticket;
import src.com.eventticketing.model.User;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.util.List;

public class MyTicketView extends JFrame {

    private User currentUser;
    private TicketDAO ticketDAO;
    private EventDAO eventDAO; 

    public MyTicketView(User user) {
        this.currentUser = user;
        this.ticketDAO = new TicketDAO();
        this.eventDAO = new EventDAO();

        setTitle("Riwayat Tiket Saya - Event Ticketing");
        setSize(750, 450);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE); // Agar aplikasi tidak tertutup total saat jendela ini diclose
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // --- Header ---
        JLabel lblTitle = new JLabel("Tiket Saya", SwingConstants.CENTER);
        lblTitle.setFont(new Font("Arial", Font.BOLD, 22));
        lblTitle.setBorder(BorderFactory.createEmptyBorder(20, 0, 20, 0));
        add(lblTitle, BorderLayout.NORTH);

        // --- Tabel Riwayat ---
        String[] columns = {"ID Tiket", "Nama Event", "Tanggal Beli", "Jml", "Total Harga", "Status"};
        DefaultTableModel model = new DefaultTableModel(columns, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false; // Agar tabel tidak bisa diedit
            }
        };
        
        JTable table = new JTable(model);
        table.setRowHeight(25);
        
        // Panggil fungsi untuk mengambil data dari Database
        loadData(model);

        JScrollPane scrollPane = new JScrollPane(table);
        scrollPane.setBorder(BorderFactory.createEmptyBorder(10, 20, 10, 20));
        add(scrollPane, BorderLayout.CENTER);

        // --- Tombol Kembali ---
        JPanel panelBottom = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        JButton btnBack = new JButton("Tutup");
        btnBack.addActionListener(e -> this.dispose());
        
        panelBottom.add(btnBack);
        add(panelBottom, BorderLayout.SOUTH);
    }

    private void loadData(DefaultTableModel model) {
        // Ambil semua tiket milik user ini
        List<Ticket> tickets = ticketDAO.getTicketsByUser(currentUser.getUserId());
        
        for (Ticket t : tickets) {
            // Kita perlu mengambil Nama Event (karena di tabel tiket cuma ada ID Event)
            Event ev = eventDAO.getEventById(t.getEventId());
            String namaEvent = (ev != null) ? ev.getNamaEvent() : "Event Tidak Ditemukan";

            model.addRow(new Object[]{
                t.getTicketId(),
                namaEvent,
                t.getTanggalBeli(),
                t.getJumlah(),
                "Rp " + t.getTotalHarga(),
                t.getStatus()
            });
        }
        
        if (tickets.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Anda belum memiliki riwayat pembelian tiket.");
        }
    }
}