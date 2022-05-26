import mysql.connector
import random


mydb = mysql.connector.connect(
  host="localhost",
  user="lbaz",
  password="#@B5d1be",
  database="polytech_absences"
)
mycursor = mydb.cursor()

# table de filieres
fp = open("d/fil.txt", 'r')
filieres = fp.read().splitlines()
fp.close()
index=1
ok_fil=True
if ok_fil :
	for fil in filieres:
		sql = "INSERT INTO `filiere` (`id_f`, `nom`) VALUES (%s, %s)"
		val = (index, fil)
		index += 1
		mycursor.execute(sql, val)
		mydb.commit()

print("table filiere ...")


# table de cours
ok_cours=True
cours = [0 for x in range(8*3+1)] 
index = 1
for fil in range(0,8):
	for ann in range(0,3):
		cours[fil*3+ann] = index
		fp = open("d/"+str(fil+1)+str(ann+3)+".txt", 'r')
		for cour in fp.read().splitlines():
			if ok_cours :
				sql = "INSERT INTO `cours` (`id_c`, `nom`, `id_f`, `annee`) VALUES (%s, %s, %s, %s)"
                val = (index, cour, fil+1, str(ann+3))
				mycursor.execute(sql, val)
				mydb.commit()
			index += 1
		cours[fil*3+ann+1] = index		

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

index = 1
ok_fil=True
if ok_fil :
	for fil in range(0,8):
		for ann in range(0,3):
			for grp in range(1, 4):
				for i in range(0, 11):
					login, password, email, prenom, nom = genetate_user(utilisateurs[index])

					# insert into utilisateur
					ok_utilisateur=True
					if ok_utilisateur:
						sql = "INSERT INTO `utilisateur` (`id_u`, `login`, `password`, `email`, `role`, `etat`, `prenom`, `nom`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
						val = (index, login, password, email, "e", 1, prenom, nom)
						mycursor.execute(sql, val)

					# insert into etudiant (user, filliere, annee) 
					ok_etudiant=True
					if ok_etudiant:
						sql = "INSERT INTO `etudiant` (`id_e, `id_f`, `annee`, `groupe`, `nbr_abscences`) VALUES (%s, %s, %s, %s, %s)"
						val = (index, fil+1, str(ann+3), grp, 0)
						mycursor.execute(sql, val)

					# insert into cours_etudiant (user, cours)
					ok_cours_etudiant=True
					if ok_cours_etudiant:
						for j in range(cours[fil*3+ann], cours[fil*3+ann+1]):
							sql = "INSERT INTO `ametice` (`id_e`, `id_c`) VALUES (%s, %s)"
							val = (index, str(j))
							# print(val, cours[fil*3+ann], cours[fil*3+ann+1])
							mycursor.execute(sql, val)
					index += 1
					mydb.commit()
					# print(index)

print("table etudiant et utilisateur...")

# table enseignement
ok_professeur=True
ok_utilisateur=True
index=793
i=1
while i<cours[-1]:
	# insert into utilisateur
	if ok_utilisateur:
		login, password, email, prenom, nom = genetate_user(utilisateurs[index])
		sql = "INSERT INTO `utilisateur` (`id_u`, `login`, `password`, `email`, `role`, `etat`, `prenom`, `nom`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
		val = (index, login, password, email, "p", 1, prenom, nom)
		mycursor.execute(sql, val)
		mydb.commit()
	
	# insert into professeur
	sql = "INSERT INTO `enseignement` (`id_c`, `id_p`, `type`, `groupe`) VALUES (%s, %s, %s, %s);"
	j=1
	while j<5 and  i<cours[-1]:
		if ok_professeur:
			val = (index, str(i), 'cm', 0) # 0 pour groupe signifier tout la promotion
			mycursor.execute(sql, val)
			for grp in range(1,4):
				for tp in ['td', 'tp']:
					val = (index, i, tp, grp) # 0 pour groupe signifier tout la promotion
					mycursor.execute(sql, val)
			mydb.commit()
		j+=1
		i+=1
	index+=1

print("table enseignement et utilisateur...")


# table admin
ok_admin=True
i=1
while i<12:
	if ok_admin:
		login, password, email, prenom, nom = genetate_user(utilisateurs[index])
		sql = "INSERT INTO `utilisateur` (`id_u`, `login`, `password`, `email`, `role`, `etat`, `prenom`, `nom`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
		val = (index, login, password, email, "a", 1, prenom, nom)
		mycursor.execute(sql, val)
		mydb.commit()
	i+=1
	index+=1

print("table utilisateur...")


# ok_absence=True
# jours=random.sample(range(1, 30), 10)
# heures=[8,10,14,16]

# # choisir des dates (rand)
# # pour chaque cours, mettre au hasard la moitie de promotion 

# for j in range(10):
# 	h = random.randint(0, 3)
# 	a = random.sample(range(0, 32), 10)
# 	date = "2022-04-{} {}:00:00".format(jours[j], heures[h])
	
	
# id_etudiant=1
# id_cours=1
# for fil in range(0,8):
# 	for ann in range(0,3):
# 		c = cours[fil*3+ann]
