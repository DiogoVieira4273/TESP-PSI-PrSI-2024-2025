package pt.ipleiria.estg.projeto.produtosginasio;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

public class ipActivity extends AppCompatActivity {
    private String IP;
    SharedPreferences sharedPreferences;
    SharedPreferences.Editor editor;
    private EditText etIp;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ip);

        etIp = findViewById(R.id.etIP);
    }

    public void onClickLigarServidor(View view) {
        String ip = etIp.getText().toString();

        String txtIP = ip;

        sharedPreferences = getPreferences(Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();

        IP = sharedPreferences.getString("IP", "");

        if (IP.isEmpty()) {
            editor.putString("IP", txtIP);
            editor.apply();
        }
        //passar para o login
        Intent intent = new Intent(getApplicationContext(), LoginActivity.class);
        startActivity(intent);
        finish();
    }
}