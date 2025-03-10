# SnapScribe

## ✍️ 概要
YouTube動画での学びをタイムスタンプ付きで即座に記録し、振り返りを容易にするためのツールです。

## 📎 URL
[SnapScribe](https://snapscribe.mikufolio.site/)

## ⚙️ 使用技術

- **Laravel**: 10.48.27
- **Inertia.js**: 1.3.0  
- **Vue.js**: 3.5.13
- **PHP**: 8.1.22
- **MySQL (RDS)**: 8.0.40
- **Nginx**
- **AWS**
    - VPC
    - EC2 (Ubuntu 24.04)
    - RDS
- **Youtube API**


## 💡使用画面と機能一覧
|トップページ|ログイン・新規登録画面|
|:--|:--|
|<img width="650" alt="Image" src="https://github.com/user-attachments/assets/57d27143-f5f8-42e3-8fa6-6a851ea02489" />|<img width="700" alt="Image" src="https://github.com/user-attachments/assets/4f951865-6788-4c23-95df-91da73610a33" />|
|SnapScribeの特徴や利用シーンといったサービスの紹介をしています。|アカウント登録画面では、必須項目を*で示し、パスワードの条件も記載しています。|

|ノート一覧画面(自分のノートタブ)|ノートの絞り込み・並び替え|
|:--|:--|
|<img width="550" alt="Image" src="https://github.com/user-attachments/assets/291d96e5-cda0-48b6-98ae-a51ba28f74d8" />|<img width="1300" alt="Image" src="https://github.com/user-attachments/assets/a70ca0b4-0c2a-4a02-a8a8-bcac5f222df1" />|
|ユーザーが作成したノート一覧です。サムネイル、タイトル、紐付けられたタグが表示されます。タイトル横の3つのドットから、ノートのタイトル・タグの編集、またはノートの削除ができます。|ノート一覧画面では、タグによってフィルタをかけたり、ノートの表示順を変更することが可能です。|

|エクスプロアタブ|動画検索|
|:--|:--|
|<img width="600" alt="Image" src="https://github.com/user-attachments/assets/16ec6fa9-5c51-475a-a80e-acd39d45146c" />|<img width="1300" alt="Image" src="https://github.com/user-attachments/assets/0fa978a3-2014-4b59-85cc-b3196ed03e42" />|
|エクスプロアタブではおすすめ動画一覧が表示されます。これらの動画はカテゴリ別に整理されています。(おすすめ動画はYoutube APIのクォータ制限を越えた場合でも視聴することができます。)|好きなYoutuber名やキーワードでYoutube動画を検索できます。関連する検索結果が50個表示されます。|

|ノート作成画面|ノート詳細設定機能|
|:--|:--|
|<img width="1200" alt="Image" src="https://github.com/user-attachments/assets/7accccfb-4cae-445c-85de-84896362541f" />|<img width="500" alt="Image" src="https://github.com/user-attachments/assets/287574a2-52ab-4800-90b5-a4fbd1b19e13" />|
|新しく作成されたノートのページです。右側にはタイムスタンプ付きで作成されたスナップが表示されます。新しくスナップを作成するには、右上の「スナップを作成」ボタンをクリックします。|ノート表示画面の「詳細設定」ボタンをクリックすると、タイトルの編集やタグ関連の操作ができます。タグの作成や検索、検索をかけたタグにhoverすると編集や削除を行えます。|

|タイムスタンプ機能|タイムスタンプ重複防止機能|
|:--|:--|
|<img width="700" alt="Image" src="https://github.com/user-attachments/assets/d7746309-b428-4abc-ba05-1a56ec9ca6e2" />|<img width="700" alt="Image" src="https://github.com/user-attachments/assets/150ef106-e616-44e1-9cc9-09f83691ebb0" />|
|スナップのタイムスタンプ箇所をクリックすると、そのタイムスタンプ箇所に動画を進めたり戻したりできます。|すでに同じタイムスタンプのスナップが存在する場合、ユーザーに通知します。|


