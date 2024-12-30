const admin = require('firebase-admin');
const serviceAccount = require('./path/to/your/serviceAccountKey.json');

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount),
  databaseURL: 'https://projetzoo.firebaseio.com/'
});

const db = admin.database();
module.exports = db;
