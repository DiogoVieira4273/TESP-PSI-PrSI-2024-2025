package pt.ipleiria.estg.projeto.produtosginasio;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;

import androidx.activity.EdgeToEdge;
import androidx.annotation.NonNull;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.graphics.Insets;
import androidx.core.view.GravityCompat;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;

import com.google.android.material.navigation.NavigationView;

public class MenuMainActivity extends AppCompatActivity implements NavigationView.OnNavigationItemSelectedListener {
    private NavigationView navigationView;
    private DrawerLayout drawer;
    private FragmentManager fragmentManager;
    private String username;
    private String email;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_menu_main);

        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        drawer = findViewById(R.id.drawerLayout);
        navigationView = findViewById(R.id.navView);

        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(this,
                drawer, toolbar, R.string.ndOpen, R.string.ndClose);
        toggle.syncState();
        drawer.addDrawerListener(toggle);

        //chamar o listenner - neste caso fica à escuta de um clique
        navigationView.setNavigationItemSelectedListener(this);

        fragmentManager = getSupportFragmentManager();
    }

    //criar Listenner para o menu
    @Override
    public boolean onNavigationItemSelected(@NonNull MenuItem item) {
        Fragment fragment = null;
        if (item.getItemId() == R.id.navPaginaInicial) {
            setTitle(item.getTitle());

            //fragment = new EstaticoFragment();
            //fragment = new ListaLivrosFragment();

        } else if (item.getItemId() == R.id.navProdutos) {
            setTitle(item.getTitle());

            //fragment = new DinamicoFragment();

        } else if (item.getItemId() == R.id.navCarrinhoCompras) {
            setTitle(item.getTitle());

            //fragment = new DinamicoFragment();

        } else if (item.getItemId() == R.id.navFavoritos) {
            setTitle(item.getTitle());

            //fragment = new DinamicoFragment();

        } else if (item.getItemId() == R.id.navPerfil) {
            setTitle(item.getTitle());

            //fragment = new DinamicoFragment();

        } else if (item.getItemId() == R.id.navHistoricoCompras) {
            setTitle(item.getTitle());

            //fragment = new DinamicoFragment();

        } else if (item.getItemId() == R.id.navEncomendas) {
            setTitle(item.getTitle());

            //fragment = new DinamicoFragment();

        } else if (item.getItemId() == R.id.navTerminarSessao) {
            setTitle(item.getTitle());

            //fragment = new DinamicoFragment();

        }

        if (fragment != null) {
            fragmentManager.beginTransaction().replace(R.id.contentFragment, fragment).commit();
        }

        drawer.closeDrawer(GravityCompat.START);

        return true;
    }
}