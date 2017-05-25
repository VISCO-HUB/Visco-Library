/*  
[INFO] 

NAME = Fix Materials
VERSION = 1.0.0
AUTHOR = Vasily Lukyanenko
DEV = VISCO
HELP = 

[1.0.0]

* First release =
[ABOUT]

This script helps to prepare materials for models before upload to Assets Library=

[TEST]

[SCRIPT]
*/	

try(closeRolloutFloater fAssetsLibraryFixMaterials)catch()
global fAssetsLibraryFixMaterials = newRolloutFloater "Fix Materials" 220 200

fn getScriptInfo s releaseInfo: "" =
(		
	if(releaseInfo != "") do
	(
		r = getINISetting s releaseInfo
		return r
	)
	
	v = getINISetting s "INFO" "VERSION"
	a = getINISetting s "INFO" "AUTHOR"
	n = getINISetting s "INFO" "NAME"
	o = getINISetting s "ABOUT"
	c = getINISetting s "INFO" "DEV"
	h = getINISetting s "INFO" "HELP"
	
	r = for i in (getINISetting s) where i != "ABOUT" and i != "SCRIPT" and i != "COPY" collect i
	
	return #(n, a, v, o, r, c, h)
)

rollout _rFixMaterials "Diffuse Gamma" 
(
	button btnSetGammaContrast "Set Gamma/Contrast" across: 2 align: #left
	spinner spnDiffuseGamma "" range: [0.1, 3.0, 0.45] type: #float fieldWidth: 50 align: #left offset: [29, 3]
	
	fn FixDiffuseGamma gamma: 0.45 =
	(
		mats = #()
		sel = selection as array

		if(sel.count == 0) do return MessageBox "Please select objects!" title: "Warning!"

		for o in sel where o.material != undefined do join mats (getClassInstances VRayMtl target:o.material)

		for m in mats do 
		(
			t_b = m.texmap_diffuse
			d = m.diffuse
						
			cc = undefined
			if(t_b != undefined and classOf t_b == ColorCorrection and t_b.name == "Gamma Fix") then
			(
				cc = t_b
			) else
			(
				cc = ColorCorrection()				
				cc.map = m.texmap_diffuse
				m.texmap_diffuse	= cc
			)
						
			cc.name = "Gamma Fix"
			cc.color = d
			cc.gammaRGB = gamma
			cc.lightnessMode = 1					
		)
		
		messageBox "Diffuse Gamma/Contrast Fixed!" title: "Done!"
	)
	
	on btnSetGammaContrast pressed do FixDiffuseGamma gamma: spnDiffuseGamma.value
)

rollout _rAbout "About" 
(
	label lblName "" 
	label lblVer "" 
	
	label lblAuthor "" height: 30
	label lblCopy ""  height: 30
	
	on _rAbout open do
	(		
		i = getScriptInfo (getThisScriptFilename())
			
		lblName.caption = i[1]
		lblAuthor.caption = i[2]
		lblVer.caption = i[3]
		lblCopy.caption = i[6]
	)
)

addRollout _rFixMaterials fAssetsLibraryFixMaterials rolledUp:false
addRollout _rAbout fAssetsLibraryFixMaterials rolledUp:true 