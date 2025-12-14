package src.com.eventticketing.view.auth;

import src.com.eventticketing.model.User;
import src.com.eventticketing.model.Admin;
import src.com.eventticketing.view.user.UserDashboard;
import src.com.eventticketing.view.admin.AdminDashboard;

import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

public class LoginView extends JFrame {

    private JTextField txtEmail;
    private JPasswordField txtPassword;
    private JButton btnLogin, btnRegister;

    public LoginView() {
        super("Login - Event Ticketing (Mode UI Demo)");
        
        setSize(400, 300);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null); 
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

        // --- EVENT HANDLING (LOGIKA DUMMY) ---
        
        // 1. Aksi Tombol Login
        btnLogin.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                prosesLoginDummy();
            }
        });

        // 2. Aksi Tombol Register
        btnRegister.addActionListener(e -> {
            JOptionPane.showMessageDialog(null, "Fitur Belum ADA");
        });
    }

    private void prosesLoginDummy() {
        String email = txtEmail.getText();
        String password = new String(txtPassword.getPassword());

        if (email.isEmpty() || password.isEmpty()) {
            JOptionPane.showMessageDialog(this, "Email dan Password harus diisi!");
            return;
        }

        // --- LOGIKA HARDCODED (TANPA DATABASE) ---
        
        // Skenario 1: Login sebagai USER
        if (email.equals("user") && password.equals("user")) {
            // Buat objek sementara 
            User dummyUser = new User(1, "User", email, password, null);
            
            JOptionPane.showMessageDialog(this, "Login Berhasil!\nHalo, " + dummyUser.getNama());
            new UserDashboard(dummyUser).setVisible(true);
            this.dispose();
        } 
        // Skenario 2: Login sebagai ADMIN
        else if (email.equals("admin") && password.equals("admin")) {
            // Buat objek sementara
            Admin dummyAdmin = new Admin(1, "Admin", email, password, null);
            
            JOptionPane.showMessageDialog(this, "Login Berhasil!\nHalo, " + dummyAdmin.getNama());
            new AdminDashboard(dummyAdmin).setVisible(true); 
            this.dispose();
        } 
        else {
            JOptionPane.showMessageDialog(this, 
                "Login Gagal! gunakan:\nUser: user/admin \n Pass: user/admin", 
                "Info", JOptionPane.INFORMATION_MESSAGE);
        }
    }
}