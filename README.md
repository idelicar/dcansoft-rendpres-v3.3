#DCanSoft TrendPres v4.0 - Ultimate WooCommerce Trendyol Entegrasyonu
https://assets/banner.png

🌟 Premium Özellikler
🚀 Akıllı Ürün Çekme
1-Tıkla Aktarma: Trendyol linkiyle ürünleri otomatik al

Çoklu Format Desteği: URL, CSV veya API bağlantısı

Gerçek Zamanlı Önizleme: Ürünler eklenmeden önce görüntüle

📊 Güçlü Analitik
Diagram
Code
🛠 Teknik Özellikler
Bileşen	Minimum Gereksinim	Önerilen
PHP	7.4	8.0+
WordPress	5.8	6.2+
WooCommerce	5.5	7.0+
MySQL	5.6	8.0
🎯 Kullanım Senaryoları
Dropshipping Mağazaları

bash
# Toplu ürün aktarma
wp dcansoft import --file=urunler.csv --type=trendyol
Fiyat Karşılaştırma Siteleri

php
// API Kullanımı
$product = DCansoft_API::get_product('trendyol_ID');
Stok Senkronizasyonu

javascript
// Otomatik senkronizasyon
setInterval(syncProducts, 3600000); // Her saat
📦 Kurulum Paketi İçeriği
text

dcansoft-trendpres/
├── assets/               # Görsel dosyalar
│   ├── css/              # Admin stilleri
│   ├── js/               # Interaktif scriptler
│   └── img/              # Logo ve bannerlar
├── includes/             # Çekirdek fonksiyonlar
│   ├── class-api.php      # API işlemleri
│   └── class-importer.php # Veri aktarımı
├── languages/            # Çeviri dosyaları
├── dcansoft-trendpres.php # Ana eklenti dosyası
└── uninstall.php         # Temiz kaldırma


🔐 Güvenlik Önlemleri
python
# Örnek API Doğrulama
def verify_request(request):
    api_key = request.headers.get('X-DCanSoft-Key')
    if api_key != os.getenv('DCAN_API_KEY'):
        raise PermissionError("Geçersiz erişim")
🌍 Çoklu Dil Desteği

Türkçe

İngilizce

Arapça (Beta)

Rusça (Yakında)

🛑 Sorun Giderme


bash

# Hata ayıklama modu

tail -f debug.log | grep -i "dcansoft"
Yaygın Sorunlar:

API Limit Aşımı: 429 hatası alırsanız limit artırma sayfasını ziyaret edin

Görsel Yükleme Hatası: php.ini'de upload_max_filesize değerini artırın

MySQL Timeout: wait_timeout=300 olarak ayarlayın

📜 Lisans Bilgisi

legal
Copyright (C) 2023 DCanSoft

Bu program ücretsiz yazılımdır: GNU Genel Kamu Lisansı 
koşullarına göre dağıtabilir ve/veya değiştirebilirsiniz.
📞 İletişim
Resmi Site: dcansoft.com.tr

Destek: support@dcansoft.com

https://assets/badge.png

Not: Bu doküman v4.0 sürümü için geçerlidir. Güncellemeler için sürüm notlarını kontrol edin.
