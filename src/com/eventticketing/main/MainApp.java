package src.com.eventticketing.main;
import src.com.eventticketing.view.auth.LoginView;
import javax.swing.SwingUtilities;

public class MainApp {
    public static void main(String[] args) {
        SwingUtilities.invokeLater(new Runnable() { 
            @Override 
            public void run() { 
                new LoginView().setVisible(true); // Menampilkan jendela login
            }
        });
    }
}