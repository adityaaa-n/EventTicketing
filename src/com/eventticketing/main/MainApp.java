package src.com.eventticketing.main;
import src.com.eventticketing.view.auth.LoginView;
import javax.swing.SwingUtilities;

public class MainApp {
    public static void main(String[] args) {
        // Menjalankan GUI di Thread yang aman (Event Dispatch Thread)
        SwingUtilities.invokeLater(new Runnable() {
            @Override
            public void run() {
                // Membuka Jendela Login
                new LoginView().setVisible(true);
            }
        });
    }
}