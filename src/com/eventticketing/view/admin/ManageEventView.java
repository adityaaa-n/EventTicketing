package src.com.eventticketing.view.admin;

import src.com.eventticketing.dao.EventDAO;
import src.com.eventticketing.model.Admin;
import src.com.eventticketing.model.Event;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.math.BigDecimal;
import java.sql.Date;
import java.util.List;

public class ManageEventView extends JFrame {

    private Admin currentAdmin;
    private EventDAO eventDAO;
    private DefaultTableModel tableModel;
    private JTable table;
    
    // Komponen Form
    private JTextField txtNama, txtDeskripsi, txtLokasi, txtTanggal, txtHarga, txtKuota;
    private JTextField txtIdEvent; // Hidden ID untuk edit

    public ManageEventView(Admin admin) {
        this.currentAdmin = admin;
        this.eventDAO = new EventDAO();

        setTitle("Kelola Event");
        setSize(900, 600);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // --- PANEL KIRI (FORM INPUT) ---
        JPanel panelForm = new JPanel(new GridLayout(7, 2, 10, 10));
        panelForm.setBorder(BorderFactory.createTitledBorder("Input Data Event"));
        panelForm.setPreferredSize(new Dimension(300, 0));

        panelForm.add(new JLabel("Nama Event:"));
        txtNama = new JTextField();
        panelForm.add(txtNama);

        panelForm.add(new JLabel("Deskripsi:"));
        txtDeskripsi = new JTextField();
        panelForm.add(txtDeskripsi);

        panelForm.add(new JLabel("Lokasi:"));
        txtLokasi = new JTextField();
        panelForm.add(txtLokasi);

        panelForm.add(new JLabel("Tanggal (YYYY-MM-DD):"));
        txtTanggal = new JTextField();
        panelForm.add(txtTanggal);

        panelForm.add(new JLabel("Harga (Rp):"));
        txtHarga = new JTextField();
        panelForm.add(txtHarga);

        panelForm.add(new JLabel("Kuota Tiket:"));
        txtKuota = new JTextField();
        panelForm.add(txtKuota);
        
        // Field ID Event (Sembunyikan atau disable)
        txtIdEvent = new JTextField();
        txtIdEvent.setEditable(false); 
        // panelForm.add(txtIdEvent); // Tidak perlu ditampilkan

        // Tombol Aksi Form
        JPanel panelButtons = new JPanel(new FlowLayout());
        JButton btnSimpan = new JButton("Simpan");
        JButton btnUpdate = new JButton("Update");
        JButton btnHapus = new JButton("Hapus");
        JButton btnClear = new JButton("Reset");

        panelButtons.add(btnSimpan);
        panelButtons.add(btnUpdate);
        panelButtons.add(btnHapus);
        panelButtons.add(btnClear);

        // Gabungkan Form dan Tombol dalam satu panel kiri
        JPanel leftPanelContainer = new JPanel(new BorderLayout());
        leftPanelContainer.add(panelForm, BorderLayout.NORTH);
        leftPanelContainer.add(panelButtons, BorderLayout.CENTER);
        
        JButton btnKembali = new JButton("Kembali ke Dashboard");
        leftPanelContainer.add(btnKembali, BorderLayout.SOUTH);

        add(leftPanelContainer, BorderLayout.WEST);

        // --- PANEL KANAN (TABEL DATA) ---
        String[] columns = {"ID", "Nama", "Tgl", "Lokasi", "Harga", "Kuota"};
        tableModel = new DefaultTableModel(columns, 0);
        table = new JTable(tableModel);
        loadTableData();

        add(new JScrollPane(table), BorderLayout.CENTER);

        // --- EVENT HANDLING ---

        // 1. KLIK TABEL (Isi Form dari data tabel)
        table.addMouseListener(new MouseAdapter() {
            @Override
            public void mouseClicked(MouseEvent e) {
                int row = table.getSelectedRow();
                txtIdEvent.setText(tableModel.getValueAt(row, 0).toString());
                txtNama.setText(tableModel.getValueAt(row, 1).toString());
                txtTanggal.setText(tableModel.getValueAt(row, 2).toString());
                txtLokasi.setText(tableModel.getValueAt(row, 3).toString());
                txtHarga.setText(tableModel.getValueAt(row, 4).toString());
                txtKuota.setText(tableModel.getValueAt(row, 5).toString());
                // Note: Deskripsi tidak ditampilkan di tabel agar ringkas, idealnya ambil lagi dari DB by ID
                txtDeskripsi.setText("-"); 
            }
        });

        // 2. TOMBOL SIMPAN (CREATE)
        btnSimpan.addActionListener(e -> {
            try {
                Event ev = new Event(
                    txtNama.getText(),
                    txtDeskripsi.getText(),
                    txtLokasi.getText(),
                    Date.valueOf(txtTanggal.getText()), // Format harus YYYY-MM-DD
                    new BigDecimal(txtHarga.getText()),
                    Integer.parseInt(txtKuota.getText()),
                    currentAdmin.getAdminId()
                );
                
                if (eventDAO.addEvent(ev)) {
                    JOptionPane.showMessageDialog(this, "Event Berhasil Ditambah!");
                    loadTableData();
                    clearForm();
                }
            } catch (Exception ex) {
                JOptionPane.showMessageDialog(this, "Error Input: " + ex.getMessage());
            }
        });

        // 3. TOMBOL UPDATE
        btnUpdate.addActionListener(e -> {
            try {
                int id = Integer.parseInt(txtIdEvent.getText());
                Event ev = new Event(
                    id, // ID untuk update
                    txtNama.getText(),
                    txtDeskripsi.getText(),
                    txtLokasi.getText(),
                    Date.valueOf(txtTanggal.getText()),
                    new BigDecimal(txtHarga.getText()),
                    Integer.parseInt(txtKuota.getText()),
                    currentAdmin.getAdminId()
                );
                
                if (eventDAO.updateEvent(ev)) {
                    JOptionPane.showMessageDialog(this, "Event Berhasil Diupdate!");
                    loadTableData();
                    clearForm();
                }
            } catch (Exception ex) {
                JOptionPane.showMessageDialog(this, "Pilih data dulu! Error: " + ex.getMessage());
            }
        });

        // 4. TOMBOL HAPUS
        btnHapus.addActionListener(e -> {
            try {
                int id = Integer.parseInt(txtIdEvent.getText());
                int confirm = JOptionPane.showConfirmDialog(this, "Yakin hapus event ini?");
                if (confirm == JOptionPane.YES_OPTION) {
                    if (eventDAO.deleteEvent(id)) {
                        JOptionPane.showMessageDialog(this, "Event Dihapus!");
                        loadTableData();
                        clearForm();
                    }
                }
            } catch (Exception ex) {
                JOptionPane.showMessageDialog(this, "Pilih data dulu!");
            }
        });

        // 5. TOMBOL RESET
        btnClear.addActionListener(e -> clearForm());

        // 6. TOMBOL KEMBALI
        btnKembali.addActionListener(e -> {
            new AdminDashboard(currentAdmin).setVisible(true);
            this.dispose();
        });
    }

    private void loadTableData() {
        tableModel.setRowCount(0);
        List<Event> events = eventDAO.getAllEvents();
        for (Event e : events) {
            tableModel.addRow(new Object[]{
                e.getEventId(),
                e.getNamaEvent(),
                e.getTanggal(),
                e.getLokasi(),
                e.getHarga(),
                e.getKuota()
            });
        }
    }

    private void clearForm() {
        txtIdEvent.setText("");
        txtNama.setText("");
        txtDeskripsi.setText("");
        txtLokasi.setText("");
        txtTanggal.setText("");
        txtHarga.setText("");
        txtKuota.setText("");
    }
}