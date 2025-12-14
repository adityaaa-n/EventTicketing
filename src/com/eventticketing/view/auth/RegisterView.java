package src.com.eventticketing.view.auth;

import src.com.eventticketing.dao.UserDAO;
import src.com.eventticketing.model.User;
import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class RegisterView extends JFrame {

    private JTextField txtNama;
    private JTextField txtEmail;
    private JPasswordField txtPassword;
    private JButton btnDaftar, btnKembali;

    public RegisterView() {
        super("Daftar Akun Baru");

        setSize(400, 350);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE); // Agar kalau ditutup tidak keluar dari seluruh aplikasi
        setLocationRelativeTo(null);
        setLayout(new BorderLayout());

        // Judul
        JLabel lblTitle = new JLabel("Registrasi User", SwingConstants.CENTER);
        lblTitle.setFont(new Font("Arial", Font.BOLD, 20));
        lblTitle.setBorder(BorderFactory.createEmptyBorder(20, 0, 20, 0));
        add(lblTitle, BorderLayout.NORTH);

        // Form Input
        JPanel panelForm = new JPanel(new GridLayout(4, 2, 10, 10));
        panelForm.setBorder(BorderFactory.createEmptyBorder(10, 40, 10, 40));

        panelForm.add(new JLabel("Nama Lengkap:"));
        txtNama = new JTextField();
        panelForm.add(txtNama);

        panelForm.add(new JLabel("Email:"));
        txtEmail = new JTextField();
        panelForm.add(txtEmail);

        panelForm.add(new JLabel("Password:"));
        txtPassword = new JPasswordField();
        panelForm.add(txtPassword);

        add(panelForm, BorderLayout.CENTER);

        // Tombol Action
        JPanel panelButton = new JPanel(new FlowLayout());
        btnDaftar = new JButton("Daftar Sekarang");
        btnKembali = new JButton("Kembali Login");

        panelButton.add(btnDaftar);
        panelButton.add(btnKembali);
        add(panelButton, BorderLayout.SOUTH);

        // --- EVENT HANDLING ---

        // 1. Aksi Tombol Daftar
        btnDaftar.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                prosesRegister();
            }
        });

        // 2. Aksi Tombol Kembali
        btnKembali.addActionListener(e -> {
            new LoginView().setVisible(true);
            this.dispose(); // Tutup jendela register
        });
    }

    private void prosesRegister() {
        String nama = txtNama.getText();
        String email = txtEmail.getText();
        String password = new String(txtPassword.getPassword());

        // Validasi Input Kosong
        if (nama.isEmpty() || email.isEmpty() || password.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Semua data wajib diisi!", "Peringatan", JOptionPane.WARNING_MESSAGE);
            return;
        }

        UserDAO userDAO = new UserDAO();

        // Cek apakah email sudah terpakai
        if (userDAO.isEmailExists(email)) {
            JOptionPane.showMessageDialog(this, "Email sudah terdaftar! Gunakan email lain.", "Gagal", JOptionPane.ERROR_MESSAGE);
            return;
        }

        // Simpan User Baru
        User newUser = new User(nama, email, password);
        boolean success = userDAO.register(newUser);

        if (success) {
            JOptionPane.showMessageDialog(this, "Registrasi Berhasil! Silakan Login.");
            new LoginView().setVisible(true);
            this.dispose();
        } else {
            JOptionPane.showMessageDialog(this, "Terjadi kesalahan saat mendaftar.", "Error", JOptionPane.ERROR_MESSAGE);
        }
    }
}