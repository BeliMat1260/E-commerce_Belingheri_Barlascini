# 🛒 E-commerce Belingheri Barlascini

Benvenuto nel progetto **E-commerce Belingheri Barlascini**, una piattaforma di shopping online custom sviluppata interamente in PHP nativo e MySQL. Questo progetto offre un'esperienza completa per l'utente, dalla navigazione del catalogo prodotti fino al checkout e alla gestione del proprio profilo.

## 🚀 Caratteristiche Principali

Il progetto include tutte le funzionalità essenziali per un moderno sito di e-commerce:

* **Autenticazione & Account (`/auth`, `/account`):** Registrazione, login, logout, modifica del profilo e della password.
* **Gestione Indirizzi (`/addresses`):** Aggiunta, modifica, eliminazione e impostazione di un indirizzo di spedizione predefinito.
* **Catalogo Prodotti (`products.php`, `/pages/product.php`):** Visualizzazione dei prodotti (ad es. set di palle da biliardo, acqua in bottiglia, come deducibile dalla cartella assets).
* **Carrello & Checkout (`/cart`, `checkout.php`):** Aggiunta di prodotti al carrello e processo di completamento dell'acquisto.
* **Lista dei Desideri (`/wishlist`):** Aggiunta/rimozione di articoli preferiti con controllo in tempo reale dello stato.
* **Gestione Ordini (`/orders`):** Visualizzazione dello storico ordini, dettagli specifici, annullamento e aggiornamento dello stato della spedizione.

## 🛠️ Tecnologie Utilizzate

* **Backend:** PHP (struttura modulare con inclusioni).
* **Database:** MySQL (script di inizializzazione fornito).
* **Frontend:** HTML5, CSS3 (`/assets/css/style.css`).

## 📁 Struttura del Progetto

Il codice è organizzato in moduli per separare logicamente le diverse funzionalità:

```text
E-commerce_Belingheri_Barlascini-main/
 ├── account/       # Gestione profilo utente
 ├── addresses/     # Gestione rubrica indirizzi
 ├── assets/        # Fogli di stile (CSS) e immagini (catalogo/homepage)
 ├── auth/          # Logica di login, registrazione e logout
 ├── cart/          # Logica del carrello acquisti
 ├── config/        # Funzioni di utilità generali
 ├── database/      # Dump del database SQL (my_mattiabelingheri.sql)
 ├── includes/      # Componenti UI (header, footer, menu) e configurazioni di sistema (DB, sessioni)
 ├── orders/        # Logica di piazzamento e tracciamento ordini
 ├── pages/         # Pagine statiche e pagina di dettaglio del prodotto
 ├── wishlist/      # Logica della lista dei desideri
 ├── index.php      # Homepage dell'e-commerce
 ├── products.php   # Pagina catalogo prodotti
 └── checkout.php   # Pagina di pagamento/conferma ordine
```

## ⚙️ Installazione e Configurazione

Per eseguire questo progetto sul tuo ambiente locale, segui questi passaggi:

1. **Requisiti di Sistema:** Assicurati di avere un server web locale con supporto PHP e MySQL (es. XAMPP, MAMP, LAMP).
2. **Clona/Estrai il Progetto:** Posiziona l'intera cartella all'interno della directory root del tuo server web (es. `htdocs` per XAMPP).
3. **Setup del Database:**
   * Apri phpMyAdmin (o il tuo client MySQL preferito).
   * Crea un nuovo database.
   * Importa il file `database/my_mattiabelingheri.sql` per generare le tabelle necessarie e popolare i dati iniziali.
4. **Configurazione DB:** 
   * Apri il file (presumibilmente situato in `includes/config/database.php`).
   * Aggiorna le costanti di connessione (host, username, password, nome database) affinché corrispondano al tuo ambiente locale.
5. **Avvio:** Apri il browser e naviga verso `http://localhost/E-commerce_Belingheri_Barlascini-main/index.php`.

---
*Sviluppato per il corso di informatica/sviluppo web.*