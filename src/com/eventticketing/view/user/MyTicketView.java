package src.com.eventticketing.view.user;

import src.com.eventticketing.dao.EventDAO;
import src.com.eventticketing.dao.TicketDAO;
import src.com.eventticketing.model.Event;
import src.com.eventticketing.model.Ticket;
import src.com.eventticketing.model.User;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.util.List;

public class MyTicketView extends JFrame {

    private User currentUser;
    private TicketDAO ticketDAO;
    private EventDAO eventDAO; 
    private JTable table;
    private DefaultTableModel model;
    private List<Ticket> tickets; // Simpan list tiket di memori agar mudah diambil

    public MyTicketView(User user) {
        this.currentUser = user;
        this.ticketDAO = new TicketDAO();
        this.eventDAO = new EventDAO();

        setTitle("Riwayat Tiket Saya");
        setSize(750, 450);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // Header
        JLabel lblTitle = new JLabel("Klik pada tiket untuk melihat detail (E-Ticket)", SwingConstants.CENTER);
        lblTitle.setFont(new Font("Arial", Font.BOLD, 16));
        lblTitle.setBorder(BorderFactory.createEmptyBorder(15, 0, 15, 0));
        add(lblTitle, BorderLayout.NORTH);

        // Tabel
        String[] columns = {"ID Tiket", "Nama Event", "Tanggal Beli", "Status"};
        model = new DefaultTableModel(columns, 0) {
            @Override
            public boolean isCellEditable(int row, int column) { return false; }
        };
        
        table = new JTable(model);
        table.setRowHeight(30);
        loadData(model);

        add(new JScrollPane(table), BorderLayout.CENTER);

        // --- FITUR BARU: KLIK TABEL BUKA DETAIL ---
        table.addMouseListener(new MouseAdapter() {
            @Override
            public void mouseClicked(MouseEvent e) {
                int selectedRow = table.getSelectedRow();
                if (selectedRow != -1) {
                    // Ambil objek ticket dari List berdasarkan index baris
                    Ticket selectedTicket = tickets.get(selectedRow);
                    
                    // Ambil detail event-nya juga
                    Event event = eventDAO.getEventById(selectedTicket.getEventId());
                    
                    if (event != null) {
                        // Buka Jendela Detail (E-Ticket)
                        new TicketDetailView(currentUser, selectedTicket, event).setVisible(true);
                    }
                }
            }
        });

        // Tombol Tutup
        JButton btnBack = new JButton("Tutup");
        btnBack.addActionListener(e -> this.dispose());
        add(btnBack, BorderLayout.SOUTH);
    }

    private void loadData(DefaultTableModel model) {
        // Simpan ke variabel global 'tickets' agar bisa diakses saat klik mouse
        tickets = ticketDAO.getTicketsByUser(currentUser.getUserId());
        
        for (Ticket t : tickets) {
            Event ev = eventDAO.getEventById(t.getEventId());
            String namaEvent = (ev != null) ? ev.getNamaEvent() : "Event Dihapus";

            model.addRow(new Object[]{
                t.getTicketId(),
                namaEvent,
                t.getTanggalBeli(),
                t.getStatus()
            });
        }
    }
}