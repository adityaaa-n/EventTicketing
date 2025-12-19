package src.com.eventticketing.view.auth;

import src.com.eventticketing.dao.UserDAO;
import src.com.eventticketing.dao.AdminDAO;
import src.com.eventticketing.model.User;
import src.com.eventticketing.model.Admin;
import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class LoginView extends JFrame {

    private JTextField txtEmail;
    private JPasswordField txtPassword;
    private JButton btnLogin, btnRegister;

    public LoginView() {
        super("Login - Event Ticketing");
        
        // Setting Jendela Utama
        setSize(400, 300);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null); // Posisi di tengah layar
        setLayout(new BorderLayout());

        // Panel Judul
        JLabel lblTitle = new JLabel("Selamat Datang", SwingConstants.CENTER);
        lblTitle.setFont(new Font("Arial", Font.BOLD, 24));
        lblTitle.setBorder(BorderFactory.createEmptyBorder(20, 0, 20, 0));
        add(lblTitle, BorderLayout.NORTH);

        // Panel Form Input
        JPanel panelForm = new JPanel(new GridLayout(3, 2, 10, 10));
        panelForm.setBorder(BorderFactory.createEmptyBorder(20, 40, 20, 40));

        panelForm.add(new JLabel("Email:"));
        txtEmail = new JTextField();
        panelForm.add(txtEmail);

        panelForm.add(new JLabel("Password:"));
        txtPassword = new JPasswordField();
        panelForm.add(txtPassword);

        add(panelForm, BorderLayout.CENTER);

        // Panel Tombol
        JPanel panelButton = new JPanel(new FlowLayout());
        btnLogin = new JButton("Login");
        btnRegister = new JButton("Daftar User Baru");
        
        panelButton.add(btnLogin);
        panelButton.add(btnRegister);
        add(panelButton, BorderLayout.SOUTH);

        // --- EVENT HANDLING (Logika Tombol) ---
        
        // 1. Aksi Tombol Login
        btnLogin.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                prosesLogin();
            }
        });

        // 2. Aksi Tombol Register
        btnRegister.addActionListener(e -> {
            // Membuka jendela Register
            new RegisterView().setVisible(true);
            // Menutup jendela Login saat ini
            this.dispose();
        });
    }

    private void prosesLogin() {
        String email = txtEmail.getText();
        String password = new String(txtPassword.getPassword());

        if (email.isEmpty() || password.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Email dan Password harus diisi!");
            return;
        }

        // Cek Login sebagai USER dulu
        UserDAO userDAO = new UserDAO();
        User user = userDAO.login(email, password);

        if (user != null) {
            JOptionPane.showMessageDialog(this, "Login Berhasil sebagai User!\nHalo, " + user.getNama());
            
            // BUKA DASHBOARD USER
            // Kita kirim objek 'user' agar dashboard tahu siapa yang login
            new src.com.eventticketing.view.user.UserDashboard(user).setVisible(true);
            
            this.dispose(); // Tutup jendela login
        } else {
            // Jika gagal user, coba cek apakah dia ADMIN?
            AdminDAO adminDAO = new AdminDAO();
            Admin admin = adminDAO.login(email, password);
            
            if (admin != null) {
                JOptionPane.showMessageDialog(this, "Login Berhasil sebagai Admin!\nHalo, " + admin.getNama());
                
                // BUKA DASHBOARD ADMIN
                new src.com.eventticketing.view.admin.AdminDashboard(admin).setVisible(true);
                
                this.dispose();
            }else {
                JOptionPane.showMessageDialog(this, "Email atau Password salah!", "Login Gagal", JOptionPane.ERROR_MESSAGE);
            }
        }
    }
}