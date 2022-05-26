import mysql.connector
import random
import string


mydb = mysql.connector.connect(
  host="localhost",
  user="lbaz",
  password="#@B5d1be",
  database="polytech_absences"
)
mycursor = mydb.cursor()
def generate_password(length):
    # With combination of lower and upper case
	characters = string.ascii_letters + string.digits
	result_str = ''.join(random.choice(characters) for i in range(length))
    # print random string
	return result_str

# table de filieres
ok_fil=True
fp = open("d/fil.txt", 'r')
filieres = fp.read().splitlines()
fp.close()
sql = "INSERT INTO `filiere` (`id_f`, `nom`) VALUES (%s, %s)"
index=1
if ok_fil :
	for fil in filieres:
		val = (index, fil)
		mycursor.execute(sql, val)
		index += 1
	mydb.commit()

print("table filiere ...")


# table de cours
ok_cours=True
cours = [0 for x in range(8*3+1)] 
index = 1
sql = "INSERT INTO `cours` (`id_c`, `nom`, `id_f`, `annee`) VALUES (%s, %s, %s, %s)"
for fil in range(0,8):
	for ann in range(0,3):
		cours[fil*3+ann] = index
		fp = open("d/"+str(fil+1)+str(ann+3)+".txt", 'r')
		for c in fp.read().splitlines():
			if ok_cours :
				val = (index, c, fil+1, str(ann+3))
				mycursor.execute(sql, val)
			index += 1
		cours[fil*3+ann+1] = index		
if ok_cours:
	mydb.commit()
print("table cours ...")


# table d'utilisateurs
fp = open('d/utilisateurs.txt', 'r')
utilisateurs = fp.read().splitlines()
fp.close()

def genetate_user(line):
	lt = line.split()
	# prenom
	prenom = lt[0]
	# nom
	nom = lt[1]
	for i in range(2,len(lt)):
		nom += " " + lt[i]
	# login
	login = nom[0] + str(random.randint(1000, 100000))
	# password
	password = prenom
	# email
	email = lt[0]
	for i in range(1,len(lt)):
		email += "." + lt[i]
	email += "@poly.com"
	return login, password, email, prenom, nom


ok_etudiant=True
sql1 = "INSERT INTO `utilisateur` (`id_u`, `login`, `password`, `email`, `role`, `etat`, `prenom`, `nom`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
sql2 = "INSERT INTO `etudiant` (`id_e`, `id_f`, `annee`, `groupe`, `nbr_absences`) VALUES (%s, %s, %s, %s, %s)"
sql3 = "INSERT INTO ametice (`id_e`, `id_c`) VALUES (%s, %s)"
index = 1
if ok_etudiant :
	for fil in range(0,8):
		for ann in range(0,3):
			for grp in range(1, 4):
				for i in range(0, 11):
					login, password, email, prenom, nom = genetate_user(utilisateurs[index])

					# insert into utilisateur
					val = (index, login, generate_password(10), email, "e", 1, prenom, nom)
					mycursor.execute(sql1, val)

					# insert into etudiant 
					val = (index, fil+1, str(ann+3), grp, 0)
					mycursor.execute(sql2, val)

					# insert into ametice
					for j in range(cours[fil*3+ann], cours[fil*3+ann+1]):
						val = (index, str(j))
						mycursor.execute(sql3, val)
					index += 1
	mydb.commit()

print("table etudiant et utilisateur...")

# table enseignement
ok_enseignement=True
sql1 = "INSERT INTO `utilisateur` (`id_u`, `login`, `password`, `email`, `role`, `etat`, `prenom`, `nom`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
sql2 = "INSERT INTO `enseignement` (`id_n`, `id_c`, `id_p`, `type`, `groupe`) VALUES (%s, %s, %s, %s, %s);"
index=793
i=1
id_n=1
if ok_enseignement:
	while i<cours[-1]:
		# insert into utilisateur
		login, password, email, prenom, nom = genetate_user(utilisateurs[index])
		val = (index, login, generate_password(10), email, "p", 1, prenom, nom)
		mycursor.execute(sql1, val)
		mydb.commit()
		
		# insert into professeur
		j=1
		while j<5 and  i<cours[-1]:
			val = (id_n, i, index, 'cm', 0) # 0 pour groupe signifier tout la promotion
			mycursor.execute(sql2, val)
			for grp in range(1,4):
				for tp in ['td', 'tp']:
					id_n += 1
					val = (id_n, i, index, tp, grp) # 0 pour groupe signifier tout la promotion
					mycursor.execute(sql2, val)
				
			j+=1
			i+=1
			id_n += 1
		index+=1
		id_n += 1
	mydb.commit()

print("table enseignement et utilisateur...")


# table admin
ok_admin=True
nbr_admin=12
i=0
sql = "INSERT INTO `utilisateur` (`id_u`, `login`, `password`, `email`, `role`, `etat`, `prenom`, `nom`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
if ok_admin:
	index += 1
	while i<nbr_admin:
		login, password, email, prenom, nom = genetate_user(utilisateurs[index])
		val = (index, login, generate_password(10), email, "a", 1, prenom, nom)
		mycursor.execute(sql, val)
		mydb.commit()
		index += 1
		i += 1

print("table utilisateur...")